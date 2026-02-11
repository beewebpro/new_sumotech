<?php

namespace App\Services\BookScrapers;

use Illuminate\Support\Facades\Http;

class VietNamThuQuanScraper
{
    protected string $url;
    protected array $chapters = [];
    protected array $bookInfo = [];
    protected int $tuaId = 0;
    protected string $cookieHeader = '';

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Scrape book info and chapter list from vietnamthuquan.eu
     */
    public function scrape(): array
    {
        try {
            $html = $this->fetchMainPage();
            if (!$html) {
                return ['error' => 'Không thể tải trang web. Vui lòng kiểm tra URL.'];
            }

            $this->extractTuaId($html);
            if ($this->tuaId === 0) {
                return ['error' => 'Không tìm thấy ID sách (tuaid) trên trang.'];
            }

            $this->scrapeBookInfo($html);
            $this->scrapeChapterList($html);

            if (empty($this->chapters)) {
                return ['error' => 'Không tìm thấy chương nào.'];
            }

            return [
                'success' => true,
                'title' => $this->bookInfo['title'] ?? 'Sách không xác định',
                'author' => $this->bookInfo['author'] ?? null,
                'category' => $this->bookInfo['category'] ?? null,
                'description' => $this->bookInfo['description'] ?? null,
                'cover_image' => $this->bookInfo['cover_image'] ?? null,
                'chapters' => $this->chapters,
                'total_chapters' => count($this->chapters),
            ];
        } catch (\Exception $e) {
            return ['error' => 'Lỗi scraping: ' . $e->getMessage()];
        }
    }

    /**
     * Fetch main page with ASP.NET cookie handshake.
     * Step 1: GET → receives Set-Cookie: AspxAutoDetectCookieSupport=1, 302 redirect
     * Step 2: GET with cookie → 200 OK
     */
    protected function fetchMainPage(): ?string
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

        // Step 1: initial request to get cookie
        $response1 = Http::withHeaders([
            'User-Agent' => $userAgent,
        ])->withOptions([
            'allow_redirects' => false,
        ])->timeout(30)->get($this->url);

        // Collect Set-Cookie
        $cookies = [];
        $setCookieHeaders = $response1->header('Set-Cookie');
        if ($setCookieHeaders) {
            // header() returns single value; use getHeader on underlying response
            $rawHeaders = $response1->toPsrResponse()->getHeader('Set-Cookie');
            foreach ($rawHeaders as $raw) {
                $parts = explode(';', $raw);
                $cookies[] = trim($parts[0]);
            }
        }

        // Always include the cookie ASP.NET expects
        if (!str_contains(implode('; ', $cookies), 'AspxAutoDetectCookieSupport')) {
            $cookies[] = 'AspxAutoDetectCookieSupport=1';
        }

        $this->cookieHeader = implode('; ', $cookies);

        // Step 2: follow redirect with cookies
        $redirectUrl = $response1->header('Location');
        $fetchUrl = $redirectUrl
            ? $this->resolveUrl($redirectUrl)
            : $this->url;

        $response2 = Http::withHeaders([
            'User-Agent' => $userAgent,
            'Cookie' => $this->cookieHeader,
        ])->timeout(30)->get($fetchUrl);

        if ($response2->successful()) {
            return $response2->body();
        }

        return null;
    }

    /**
     * Resolve a relative URL against the base domain.
     */
    protected function resolveUrl(string $url): string
    {
        if (str_starts_with($url, 'http')) {
            return $url;
        }
        $parsed = parse_url($this->url);
        $base = ($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? 'vietnamthuquan.eu');
        return $base . '/' . ltrim($url, '/');
    }

    /**
     * Extract tuaid from the page JavaScript.
     * Pattern: thong_so+="&tuaid=";thong_so+="2936";
     * Or: noidung1('tuaid=2936&chuongid=1')
     */
    protected function extractTuaId(string $html): void
    {
        if (preg_match('/tuaid=(\d+)/', $html, $m)) {
            $this->tuaId = (int) $m[1];
        }
    }

    /**
     * Extract book title and author from the page.
     */
    protected function scrapeBookInfo(string $html): void
    {
        $this->bookInfo = [];

        // Title from <title> tag: "Mời đọc tác phẩm: Người Mohican Cuối Cùng, - Trang Sách..."
        if (preg_match('/<title>Mời đọc tác phẩm:\s*([^,<]+)/iu', $html, $m)) {
            $this->bookInfo['title'] = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
        } elseif (preg_match('/<title>([^<]+)</i', $html, $m)) {
            $title = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
            $title = preg_replace('/\s*-\s*Trang Sách.*$/iu', '', $title);
            $this->bookInfo['title'] = trim($title);
        }

        // Author: try to get from the first chapter AJAX response (Part 1 contains author)
        // We'll fetch chapter 1 to get author info
        $chapterData = $this->fetchChapterRaw(1);
        if ($chapterData && isset($chapterData[1])) {
            $titlePart = $chapterData[1];
            // Pattern: <title_text>\n author_name
            $cleaned = strip_tags($titlePart);
            $lines = array_filter(array_map('trim', explode("\n", $cleaned)));
            $lines = array_values($lines);
            if (count($lines) >= 2) {
                // First line is book title, second is author
                $this->bookInfo['author'] = $lines[1] ?? null;
            }

            // Also extract description from first chapter content
            if (isset($chapterData[2]) && empty($this->bookInfo['description'])) {
                $content = $this->cleanContent($chapterData[2]);
                if (mb_strlen($content) > 100) {
                    $this->bookInfo['description'] = mb_substr($content, 0, 500);
                }
            }
        }
    }

    /**
     * Extract chapter list from the HTML.
     * Pattern: <acronym title="Chapter Title"><li onClick="noidung1('tuaid=XXXX&chuongid=N')">Chương N</li></acronym>
     */
    protected function scrapeChapterList(string $html): void
    {
        $this->chapters = [];
        $seen = [];

        // Match: <acronym title="TITLE">...<li onClick="noidung1('tuaid=XXXX&chuongid=N')">
        if (preg_match_all(
            '/<acronym\s+title="([^"]*)"[^>]*>.*?chuongid=(\d+).*?<\/acronym>/is',
            $html,
            $matches,
            PREG_SET_ORDER
        )) {
            foreach ($matches as $match) {
                $chapterTitle = html_entity_decode(trim($match[1]), ENT_QUOTES, 'UTF-8');
                $chuongId = (int) $match[2];

                if ($chuongId <= 0 || isset($seen[$chuongId])) {
                    continue;
                }
                $seen[$chuongId] = true;

                // Build a virtual "url" that encodes tuaid + chuongid
                // The controller calls scrapeChapterContent($url) so we encode params
                $virtualUrl = "vntq://{$this->tuaId}/{$chuongId}";

                $displayTitle = $chapterTitle ?: "Chương {$chuongId}";

                $this->chapters[] = [
                    'number' => $chuongId,
                    'title' => $displayTitle,
                    'url' => $virtualUrl,
                ];
            }
        }

        // Sort by chapter number
        usort($this->chapters, fn($a, $b) => $a['number'] <=> $b['number']);

        // Re-number sequentially
        foreach ($this->chapters as $i => &$ch) {
            $ch['number'] = $i + 1;
        }
    }

    /**
     * Fetch chapter content. Called by controller with the virtual URL.
     */
    public function scrapeChapterContent(string $chapterUrl): string
    {
        try {
            // Parse virtual URL: vntq://2936/1
            if (preg_match('/vntq:\/\/(\d+)\/(\d+)/', $chapterUrl, $m)) {
                $tuaId = (int) $m[1];
                $chuongId = (int) $m[2];
            } else {
                return '';
            }

            $parts = $this->fetchChapterRaw($chuongId, $tuaId);
            if (!$parts || !isset($parts[2])) {
                return '';
            }

            return $this->cleanContent($parts[2]);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Fetch raw chapter data via AJAX POST.
     * POST to chuonghoi_moi.aspx? with body tuaid=X&chuongid=Y
     * Response split by --!!tach_noi_dung!!--
     *   [0] = HTML wrapper, [1] = title/author, [2] = content, [3] = navigation
     */
    protected function fetchChapterRaw(int $chuongId, ?int $tuaId = null): ?array
    {
        $tuaId = $tuaId ?? $this->tuaId;
        if ($tuaId <= 0 || $chuongId <= 0) {
            return null;
        }

        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';

        // Ensure we have cookies
        if (empty($this->cookieHeader)) {
            $this->cookieHeader = 'AspxAutoDetectCookieSupport=1';
        }

        $baseUrl = $this->resolveUrl('/truyen/chuonghoi_moi.aspx?');

        $response = Http::withHeaders([
            'User-Agent' => $userAgent,
            'Cookie' => $this->cookieHeader,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->timeout(30)->withBody(
            "tuaid={$tuaId}&chuongid={$chuongId}",
            'application/x-www-form-urlencoded'
        )->post($baseUrl);

        if (!$response->successful()) {
            return null;
        }

        $body = $response->body();
        $parts = explode('--!!tach_noi_dung!!--', $body);

        return count($parts) >= 3 ? $parts : null;
    }

    /**
     * Clean HTML content to plain text.
     */
    protected function cleanContent(string $html): string
    {
        // Remove scripts and styles
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/i', '', $html);

        // Convert line breaks
        $html = str_ireplace(['<br>', '<br/>', '<br />', '<BR>', '<BR/>', '<BR />'], "\n", $html);
        $html = preg_replace('/<\/p>/i', "\n\n", $html);

        // Decode entities
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        // Strip tags
        $text = strip_tags($html);

        // Normalize whitespace
        $text = preg_replace('/\n\n+/', "\n\n", $text);
        $text = preg_replace('/ +/', ' ', $text);
        $text = preg_replace('/^\s+/m', '', $text);

        return trim($text);
    }
}

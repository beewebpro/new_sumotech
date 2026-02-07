<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ApiUsageService;

class SegmentFixService
{
    public function fixSegments(array $segments, ?int $projectId = null): array
    {
        Log::info('=== [SegmentFixService] START ===', ['segment_count' => count($segments), 'first_segment' => isset($segments[0]) ? substr($segments[0]['text'] ?? '', 0, 100) : null]);

        $apiKey = env('OPENAI_API_KEY');
        Log::debug('OpenAI API Key:', ['has_key' => !empty($apiKey)]);

        if (!$apiKey) {
            Log::error('OpenAI API Key not found in .env');
            return $this->fallbackSegments($segments);
        }

        $cleanedSegments = array_map(function ($segment) {
            $text = $segment['text'] ?? '';
            $text = $this->stripStageDirections($text);
            return [
                'index' => $segment['index'],
                'text' => $text
            ];
        }, $segments);

        Log::info('Cleaned segments:', ['count' => count($cleanedSegments), 'first_5' => array_slice($cleanedSegments, 0, 2)]);

        try {
            // Detect language from first segment
            $firstSegment = $cleanedSegments[0]['text'] ?? '';
            $isVietnamese = $this->detectLanguage($firstSegment);

            $languageInstruction = $isVietnamese ?
                'The text is in Vietnamese. Fix grammar, punctuation, and improve sentence structure in Vietnamese.' :
                'The text is in English. Fix grammar, punctuation, and improve sentence structure.';

            $payload = [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional transcript editor. Your task is to:
1. Fix grammar, punctuation, and sentence structure
2. Join incomplete or fragmented sentences if they clearly belong together
3. Improve readability and flow while preserving original meaning
4. Remove stage directions and markers like [applause], [music], (laughter), (cheering), etc.
5. Fix transcription errors, stuttering, and repeated words
6. Ensure proper capitalization and spacing
7. ' . $languageInstruction . '

IMPORTANT: You MUST improve the text. Fix all grammar errors, punctuation issues, and improve sentence flow.
Do NOT preserve errors. Do NOT return text unchanged unless it is already perfect.

Return ONLY a valid JSON array, no other text.
Format: [{"index": 0, "text": "improved text"}, ...]
Preserve all indices. Keep the same language.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Fix and improve these transcript segments. Make them grammatically correct and more readable.\n\n" . json_encode($cleanedSegments, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 4000
            ];

            Log::debug('OpenAI Request Payload:', [
                'model' => $payload['model'],
                'segment_count' => count($cleanedSegments),
                'is_vietnamese' => $isVietnamese,
                'payload_size' => strlen(json_encode($payload))
            ]);

            $response = Http::timeout(60)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            Log::debug('OpenAI Response:', [
                'status' => $response->status(),
                'is_successful' => $response->successful()
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Log failure
                ApiUsageService::logFailure(
                    'OpenAI',
                    'fix_segments',
                    $response->body(),
                    $projectId,
                    ['segment_count' => count($cleanedSegments)]
                );

                return $this->fallbackSegments($cleanedSegments);
            }

            $data = $response->json();
            Log::debug('OpenAI JSON Response:', ['keys' => array_keys($data)]);

            $content = $data['choices'][0]['message']['content'] ?? '';
            Log::debug('OpenAI Message Content:', ['length' => strlen($content), 'preview' => substr($content, 0, 200)]);

            $parsed = $this->parseJsonFromResponse($content);

            Log::debug('Parsed Result:', [
                'is_array' => is_array($parsed),
                'count' => is_array($parsed) ? count($parsed) : 0,
                'parsed' => $parsed
            ]);

            if (!is_array($parsed)) {
                Log::error('OpenAI segment fix parse error', [
                    'content' => $content,
                    'parsed_result' => $parsed
                ]);

                // Log failure
                ApiUsageService::logFailure(
                    'OpenAI',
                    'fix_segments',
                    'JSON parse error: ' . substr($content, 0, 200),
                    $projectId,
                    ['segment_count' => count($cleanedSegments)]
                );

                return $this->fallbackSegments($cleanedSegments);
            }

            $output = [];
            foreach ($parsed as $item) {
                if (!isset($item['index'])) {
                    Log::warn('Item missing index:', ['item' => $item]);
                    continue;
                }
                $text = $this->stripStageDirections($item['text'] ?? '');

                // Check if text changed
                $originalText = null;
                foreach ($segments as $seg) {
                    if ($seg['index'] == $item['index']) {
                        $originalText = $seg['text'];
                        break;
                    }
                }

                $textChanged = ($originalText && trim($text) !== trim($originalText));

                $output[] = [
                    'index' => $item['index'],
                    'text' => trim($text)
                ];

                Log::debug('Segment processing', [
                    'index' => $item['index'],
                    'original' => substr($originalText ?? '', 0, 50),
                    'fixed' => substr(trim($text), 0, 50),
                    'changed' => $textChanged
                ]);
            }

            // Log success
            $tokensUsed = $data['usage']['total_tokens'] ?? count($segments) * 50;
            ApiUsageService::logOpenAI(
                'fix_segments',
                $tokensUsed,
                null,
                'gpt-3.5-turbo',
                $projectId,
                ['segment_count' => count($segments)]
            );

            Log::info('=== [SegmentFixService] SUCCESS ===', [
                'input_count' => count($segments),
                'output_count' => count($output),
                'output_sample' => array_slice($output, 0, 2)
            ]);

            return $output;
        } catch (Exception $e) {
            Log::error('OpenAI segment fix exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->fallbackSegments($cleanedSegments);
        }
    }

    private function parseJsonFromResponse(string $content)
    {
        $content = trim($content);
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Try to extract JSON array from response
        if (preg_match('/\[.*\]/s', $content, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return null;
    }

    private function detectLanguage(string $text): bool
    {
        // Check for Vietnamese Unicode characters (Latin Extended-A and other Vietnamese-specific chars)
        // Vietnamese uses: à, á, ả, ã, ạ, ă, ằ, ắ, ẳ, ẵ, ặ, â, ầ, ấ, ẩ, ẫ, ậ, etc.
        $vietnamesePattern = '/[\x{0100}-\x{017F}\x{1E00}-\x{1EFF}]/u'; // Latin Extended-A and B (Vietnamese diacritics)
        return preg_match($vietnamesePattern, $text) > 0;
    }

    private function stripStageDirections(string $text): string
    {
        $patterns = [
            '/\[(applause|music|laughter|cheering|crowd|clapping|noise|inaudible)\]/i',
            '/\((applause|music|laughter|cheering|crowd|clapping|noise|inaudible)\)/i',
            '/\b(applause|music|laughter|cheering|crowd|clapping)\b/i'
        ];

        $cleaned = preg_replace($patterns, '', $text);
        $cleaned = preg_replace('/\s+/u', ' ', $cleaned);
        return trim($cleaned);
    }

    private function fallbackSegments(array $segments): array
    {
        return array_map(function ($segment) {
            return [
                'index' => $segment['index'],
                'text' => trim($segment['text'] ?? '')
            ];
        }, $segments);
    }
}

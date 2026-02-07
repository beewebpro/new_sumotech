<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ApiUsageService;

class TranslationService
{
    private $provider;

    public function __construct($provider = null)
    {
        $this->provider = $provider ?? env('TRANSLATION_PROVIDER', 'google');
    }

    /**
     * Translate segments to Vietnamese
     * 
     * @param array $segments
     * @param string|null $provider
     * @param int|null $projectId
     * @return array
     */
    public function translateSegments(array $segments, $provider = null, ?int $projectId = null): array
    {
        if ($provider) {
            $this->provider = $provider;
        }

        $translatedSegments = [];

        foreach ($segments as $index => $segment) {
            $translatedText = $this->translate($segment['text'], 'en', 'vi');

            // Handle both 'start' and 'start_time' keys (YouTube uses 'start')
            $startTime = $segment['start'] ?? $segment['start_time'] ?? 0;
            $endTime = $segment['end_time'] ?? ($startTime + ($segment['duration'] ?? 0));
            $duration = $segment['duration'] ?? 0;

            $translatedSegments[] = [
                'index' => $index,
                'original_text' => $segment['text'],
                'text' => $translatedText,
                'start' => $startTime,
                'start_time' => $startTime,  // Keep for backward compatibility
                'end_time' => $endTime,
                'duration' => $duration
            ];
        }

        return $translatedSegments;
    }

    /**
     * Translate a single text string
     *
     * @param string $text
     * @param string $from
     * @param string $to
     * @param string|null $provider
     * @return string
     */
    public function translateText(string $text, string $from = 'en', string $to = 'vi', $provider = null): string
    {
        if ($provider) {
            $this->provider = $provider;
        }

        return $this->translate($text, $from, $to);
    }

    /**
     * Translate text using selected provider (OpenAI or Google)
     * 
     * @param string $text
     * @param string $from
     * @param string $to
     * @return string
     */
    private function translate(string $text, string $from, string $to): string
    {
        // Trim text
        $text = trim($text);
        if (empty($text)) return '';

        if ($this->provider === 'openai') {
            return $this->translateWithOpenAI($text, $from, $to);
        } else {
            return $this->translateWithGoogle($text, $from, $to);
        }
    }

    /**
     * Translate text using OpenAI
     * 
     * @param string $text
     * @param string $from
     * @param string $to
     * @return string
     */
    private function translateWithOpenAI(string $text, string $from, string $to): string
    {
        try {
            $apiKey = env('OPENAI_API_KEY');

            if (!$apiKey) {
                Log::error('OpenAI API Key not found in .env');
                return $text;
            }

            $languageMap = [
                'vi' => 'Vietnamese',
                'en' => 'English'
            ];

            $toLanguage = $languageMap[$to] ?? ucfirst($to);

            $response = Http::timeout(30)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional translator. Translate the given text accurately and naturally.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Translate the following text from English to {$toLanguage}. Only provide the translation, nothing else:\n\n{$text}"
                        ]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 1000
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['choices'][0]['message']['content'])) {
                    $translated = trim($data['choices'][0]['message']['content']);
                    Log::info('OpenAI translation success', [
                        'original' => substr($text, 0, 100),
                        'translated' => substr($translated, 0, 100)
                    ]);

                    // Log API usage
                    $tokensUsed = $data['usage']['total_tokens'] ?? strlen($text) / 4;
                    ApiUsageService::logOpenAI(
                        'translate_transcript',
                        (int) $tokensUsed,
                        null,
                        'gpt-3.5-turbo',
                        null,
                        ['text_length' => strlen($text)]
                    );

                    return $translated;
                }
            } else {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                // Log failure
                ApiUsageService::logFailure(
                    'OpenAI',
                    'translate_transcript',
                    'HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 200),
                    null,
                    ['text_length' => strlen($text)]
                );
            }

            return $text;
        } catch (Exception $e) {
            Log::error('OpenAI translation exception', [
                'error' => $e->getMessage(),
                'text' => substr($text, 0, 100)
            ]);

            // Log failure
            ApiUsageService::logFailure(
                'OpenAI',
                'translate_transcript',
                $e->getMessage(),
                null,
                ['text_length' => strlen($text)]
            );
            return $text;
        }
    }

    /**
     * Translate text using Google Translate API
     * 
     * @param string $text
     * @param string $from
     * @param string $to
     * @return string
     */
    private function translateWithGoogle(string $text, string $from, string $to): string
    {
        try {
            $apiKey = env('GOOGLE_TRANSLATE_API_KEY');

            if (!$apiKey) {
                Log::error('Google Translate API Key not found in .env');
                return $text;
            }

            // Use Google Cloud Translation API v2 with proper format
            // Important: Key must be in URL, data must be form-encoded (not JSON)
            $url = "https://translation.googleapis.com/language/translate/v2?key=" . urlencode($apiKey);

            $response = Http::timeout(15)->asForm()->post($url, [
                'q' => $text,
                'source' => $from,
                'target' => $to
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['translations'][0]['translatedText'])) {
                    $translated = html_entity_decode($data['data']['translations'][0]['translatedText']);
                    Log::info('Google Translate success', [
                        'original' => substr($text, 0, 100),
                        'translated' => substr($translated, 0, 100)
                    ]);

                    // Log API usage
                    ApiUsageService::logGoogleTranslate(
                        strlen($text),
                        null,
                        null,
                        ['source' => $from, 'target' => $to]
                    );

                    return $translated;
                }
            } else {
                Log::error('Google Translate API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                // Log failure
                ApiUsageService::logFailure(
                    'Google Translate',
                    'translate_transcript',
                    'HTTP ' . $response->status() . ': ' . substr($response->body(), 0, 200),
                    null,
                    ['text_length' => strlen($text)]
                );
            }

            return $text;
        } catch (Exception $e) {
            Log::error('Google Translate exception', [
                'error' => $e->getMessage(),
                'text' => substr($text, 0, 100)
            ]);
            return $text;
        }
    }
}

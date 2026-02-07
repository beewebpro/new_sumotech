<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AISegmentationService
{
    /**
     * Use OpenAI GPT to intelligently segment transcript into meaningful chunks
     * 
     * @param array $cleanedTranscript Cleaned transcript with timing info
     * @param string|null $projectId Project ID for progress tracking
     * @return array Semantically meaningful segments with preserved timing
     */
    public function segmentWithAI(array $cleanedTranscript, ?string $projectId = null): array
    {
        if (empty($cleanedTranscript)) {
            return [];
        }

        try {
            // Initialize progress tracking
            if ($projectId) {
                $this->setProgress($projectId, 'started', 0, 'Bắt đầu AI segmentation...');
            }

            // Step 1: Build text with timing markers for AI to understand structure
            $textWithTimings = $this->buildTextWithTimings($cleanedTranscript);

            if ($projectId) {
                $this->setProgress($projectId, 'processing', 20, 'Đang gửi tới Gemini...');
            }

            Log::info('AISegmentation: Sending to Gemini', [
                'text_length' => strlen($textWithTimings),
                'entries_count' => count($cleanedTranscript),
                'project_id' => $projectId
            ]);

            // Step 2: Send to Gemini for semantic segmentation
            $segments = $this->callGeminiForSegmentation($textWithTimings, $cleanedTranscript);

            if ($projectId) {
                $this->setProgress($projectId, 'processing', 80, 'Đang xử lý kết quả...');
            }

            Log::info('AISegmentation: Successfully segmented', [
                'segments_count' => count($segments),
                'first_segment_words' => str_word_count($segments[0]['text'] ?? ''),
                'project_id' => $projectId
            ]);

            if ($projectId) {
                $this->setProgress($projectId, 'completed', 100, 'Hoàn tất AI segmentation!');
            }

            return $segments;
        } catch (\Exception $e) {
            Log::error('AISegmentation: Error during AI segmentation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $projectId
            ]);

            // Update progress to error state
            if ($projectId) {
                $this->setProgress($projectId, 'error', 0, 'Lỗi AI segmentation, sử dụng phương pháp thường...');
            }

            // Fallback to traditional segmentation if AI fails
            Log::info('AISegmentation: Falling back to traditional segmentation');
            return app('App\Services\TranscriptSegmentationService')->segment($cleanedTranscript);
        }
    }

    /**
     * Set progress status for a project
     */
    public function setProgress(string $projectId, string $status, int $percentage, string $message): void
    {
        Cache::put("ai_segmentation_progress_{$projectId}", [
            'status' => $status,  // 'started', 'processing', 'completed', 'error'
            'percentage' => $percentage,
            'message' => $message,
            'timestamp' => now()->timestamp
        ], now()->addMinutes(30)); // Cache for 30 minutes
    }

    /**
     * Get progress status for a project
     */
    public function getProgress(string $projectId): array
    {
        return Cache::get("ai_segmentation_progress_{$projectId}", [
            'status' => 'idle',
            'percentage' => 0,
            'message' => 'Chưa bắt đầu',
            'timestamp' => now()->timestamp
        ]);
    }

    /**
     * Clear progress for a project
     */
    public function clearProgress(string $projectId): void
    {
        Cache::forget("ai_segmentation_progress_{$projectId}");
    }


    /**
     * Build text with timing markers for AI understanding
     */
    private function buildTextWithTimings(array $cleanedTranscript): string
    {
        $text = '';
        foreach ($cleanedTranscript as $entry) {
            $start = number_format($entry['start'] ?? 0, 1);
            $text .= "[{$start}s] {$entry['text']} ";
        }
        return trim($text);
    }

    /**
     * Call OpenAI API to segment transcript semantically
     */
    private function callGeminiForSegmentation(string $textWithTimings, array $cleanedTranscript): array
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');

        if (!$apiKey) {
            throw new \Exception('GEMINI_API_KEY not configured');
        }

        $prompt = $this->buildSegmentationPrompt($textWithTimings);

        Log::debug('AISegmentation: Gemini Request', [
            'prompt_length' => strlen($prompt),
            'model' => $model
        ]);

        $response = Http::timeout(60)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
            [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 2000
                ]
            ]
        );

        if (!$response->successful()) {
            throw new \Exception('Gemini API Error: ' . $response->body());
        }

        $data = $response->json();

        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$content) {
            throw new \Exception('Invalid Gemini response structure');
        }

        // Parse AI response and reconstruct segments with timing
        return $this->parseAndReconstructSegments($content, $cleanedTranscript);
    }

    /**
     * Build the prompt for OpenAI segmentation
     */
    private function buildSegmentationPrompt(string $textWithTimings): string
    {
        return <<<'PROMPT'
Below is a transcript with timing markers [seconds]. Your task is to segment this transcript into meaningful, complete thoughts or sentences.

IMPORTANT RULES:
1. Each segment must be a COMPLETE sentence or complete thought (not fragments)
2. Each segment should be 2-4 sentences on average (around 15-50 words)
3. Do NOT split in the middle of a sentence or clause
4. Preserve the exact text from the original transcript
5. Output format: JSON array with segments in order

Return ONLY valid JSON array with this structure:
[
  {
    "text": "complete sentence or thought",
    "start_marker": 0.0,
    "end_marker": 5.2
  },
  {
    "text": "next complete sentence",
    "start_marker": 5.2,
    "end_marker": 10.5
  }
]

Examples:
- BAD: "some of the most successful" (incomplete)
- GOOD: "some of the most successful people in the world are the ones who've had the most failures" (complete thought)

TRANSCRIPT:
PROMPT . $textWithTimings;
    }

    /**
     * Parse OpenAI response and reconstruct segments with proper timing from original transcript
     */
    private function parseAndReconstructSegments(string $jsonContent, array $cleanedTranscript): array
    {
        try {
            // Extract JSON from content (in case there's extra text)
            preg_match('/\[.*\]/s', $jsonContent, $matches);
            if (empty($matches)) {
                throw new \Exception('No JSON array found in response');
            }

            $aiSegments = json_decode($matches[0], true);

            if (!is_array($aiSegments)) {
                throw new \Exception('Invalid JSON structure');
            }

            Log::debug('AISegmentation: Parsed AI segments', [
                'count' => count($aiSegments),
                'first_text_length' => strlen($aiSegments[0]['text'] ?? '')
            ]);

            // Now map AI segments back to original transcript entries to get precise timing
            $reconstructedSegments = $this->mapSegmentsToTiming($aiSegments, $cleanedTranscript);

            return $reconstructedSegments;
        } catch (\Exception $e) {
            Log::error('AISegmentation: Parse error', [
                'error' => $e->getMessage(),
                'content_preview' => substr($jsonContent, 0, 500)
            ]);
            throw $e;
        }
    }

    /**
     * Map AI-suggested segments back to original entries for accurate timing
     */
    private function mapSegmentsToTiming(array $aiSegments, array $cleanedTranscript): array
    {
        $result = [];
        $transcriptText = '';
        $transcriptIndex = 0;
        $entries = [];

        // Build a searchable text mapping
        foreach ($cleanedTranscript as $idx => $entry) {
            $transcriptText .= $entry['text'] . ' ';
            $entries[] = $entry;
        }
        $transcriptText = trim($transcriptText);

        foreach ($aiSegments as $aiSegment) {
            $segmentText = $aiSegment['text'] ?? '';

            if (empty($segmentText)) {
                continue;
            }

            // Find segment text in the transcript
            $position = mb_stripos($transcriptText, $segmentText);

            if ($position === false) {
                // Try to match as close as possible
                Log::warning('AISegmentation: Exact text match not found, using closest match', [
                    'looking_for' => substr($segmentText, 0, 50)
                ]);
                continue;
            }

            // Find which entries make up this segment
            $segmentStart = null;
            $segmentEnd = null;
            $charCount = 0;
            $matchedEntries = [];

            foreach ($entries as $idx => $entry) {
                $entryText = $entry['text'];
                $entryLength = strlen($entryText) + 1; // +1 for space

                if ($charCount + $entryLength >= $position && $segmentStart === null) {
                    $segmentStart = $entry['start'] ?? 0;
                }

                if ($charCount < $position + strlen($segmentText)) {
                    $matchedEntries[] = $entry;
                    $segmentEnd = ($entry['start'] ?? 0) + ($entry['duration'] ?? 0);
                }

                $charCount += $entryLength;

                if ($charCount >= $position + strlen($segmentText)) {
                    break;
                }
            }

            if ($segmentStart !== null && $segmentEnd !== null) {
                $duration = max(0, $segmentEnd - $segmentStart);

                $result[] = [
                    'text' => $segmentText,
                    'start_time' => $segmentStart,
                    'end_time' => $segmentEnd,
                    'duration' => $duration,
                    'entries' => $matchedEntries
                ];
            }
        }

        return $result;
    }
}

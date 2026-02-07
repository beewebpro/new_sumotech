<?php

namespace App\Services;

class TranscriptCleanerService
{
    /**
     * Clean and normalize transcript
     * 
     * @param array $transcript
     * @return array
     */
    public function clean(array $transcript): array
    {
        $cleaned = [];

        foreach ($transcript as $entry) {
            $text = $entry['text'];

            // Remove extra whitespace
            $text = preg_replace('/\s+/', ' ', $text);

            // Remove special characters that might interfere with TTS
            $text = preg_replace('/[\[\]\(\)]/', '', $text);

            // Trim
            $text = trim($text);

            // Skip empty entries
            if (empty($text)) {
                continue;
            }

            $cleaned[] = [
                'text' => $text,
                'start' => $entry['start'],
                'duration' => $entry['duration']
            ];
        }

        return $cleaned;
    }
}

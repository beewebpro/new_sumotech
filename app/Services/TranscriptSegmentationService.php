<?php

namespace App\Services;

class TranscriptSegmentationService
{
    /**
     * Segment transcript into meaningful paragraphs
     * 
     * Improvements made:
     * 1. Detects and preserves gaps/silence between segments (>500ms threshold)
     * 2. Increased word count threshold from 50 to 80 words for longer, more natural paragraphs
     * 3. Increased duration threshold from 10 to 15 seconds for better segment grouping
     * 4. Requires at least 20 words before accepting sentence-ending punctuation
     * 5. Properly handles timing information with gap detection
     * 
     * @param array $cleanedTranscript
     * @return array
     */
    public function segment(array $cleanedTranscript): array
    {
        if (empty($cleanedTranscript)) {
            return [];
        }

        $segments = [];
        $currentSegment = [];
        $segmentText = '';
        $segmentStart = 0;
        $segmentDuration = 0;
        $lastEntryStart = null;
        $lastEntryDuration = null;
        $wordCount = 0;
        $previousEnd = null;

        foreach ($cleanedTranscript as $index => $entry) {
            // Detect gap/silence: if current entry starts significantly after previous entry ended
            $currentStart = $entry['start'] ?? 0;
            $hasGap = $previousEnd !== null && ($currentStart - $previousEnd) > 0.5; // 500ms threshold for gap

            if ($index === 0) {
                $segmentStart = 0; // Always start first segment from 0, not from first entry's timestamp
            }

            // If there's a significant gap AND we have accumulated text, finish current segment
            if ($hasGap && !empty($currentSegment)) {
                $this->finalizeSegment($segments, $currentSegment, $segmentText, $segmentStart, $previousEnd, $cleanedTranscript, $index - 1);

                // Reset and start new segment
                $currentSegment = [];
                $segmentText = '';
                $segmentDuration = 0;
                $wordCount = 0;
                $segmentStart = $currentStart;
                $lastEntryStart = null;
                $lastEntryDuration = null;
            }

            $currentSegment[] = $entry;
            $segmentText .= ($segmentText ? ' ' : '') . $entry['text'];
            $segmentDuration += $entry['duration'];
            $lastEntryStart = $entry['start'] ?? $lastEntryStart;
            $lastEntryDuration = $entry['duration'] ?? 0;
            $wordCount += str_word_count($entry['text']);
            $previousEnd = ($entry['start'] ?? 0) + ($entry['duration'] ?? 0);

            // Create new segment based on:
            // 1. Sentence endings (. ! ?)
            // 2. Word count threshold (increased to ~80-100 words for better paragraphs)
            // 3. Duration threshold (increased to 15-20 seconds)
            $endsWithPunctuation = preg_match('/[.!?]$/', trim($entry['text']));
            $exceedsWordLimit = $wordCount >= 80;
            $exceedsDuration = $segmentDuration >= 15;

            if (($endsWithPunctuation && $wordCount >= 20) || $exceedsWordLimit || $exceedsDuration) {
                $this->finalizeSegment($segments, $currentSegment, $segmentText, $segmentStart, $previousEnd, $cleanedTranscript, $index);

                // Reset for next segment
                $currentSegment = [];
                $segmentText = '';
                $segmentDuration = 0;
                $lastEntryStart = null;
                $lastEntryDuration = null;
                $wordCount = 0;

                if (isset($cleanedTranscript[$index + 1])) {
                    $segmentStart = $cleanedTranscript[$index + 1]['start'];
                }
            }
        }

        // Add remaining segment if any
        if (!empty($currentSegment)) {
            $rawEnd = ($lastEntryStart ?? $segmentStart) + ($lastEntryDuration ?? 0);
            $outDuration = max(0, $rawEnd - $segmentStart);

            $segments[] = [
                'text' => $segmentText,
                'start_time' => $segmentStart,
                'end_time' => $rawEnd,
                'duration' => $outDuration,
                'entries' => $currentSegment
            ];
        }

        return $segments;
    }

    /**
     * Finalize a segment with proper timing
     * 
     * @param array $segments Reference to segments array
     * @param array $currentSegment Entries in current segment
     * @param string $segmentText Combined text
     * @param float $segmentStart Start time of segment
     * @param float $previousEnd End time of last entry
     * @param array $cleanedTranscript Full transcript for reference
     * @param int $lastIndex Index of last entry in current segment
     */
    private function finalizeSegment(&$segments, $currentSegment, $segmentText, $segmentStart, $previousEnd, $cleanedTranscript, $lastIndex)
    {
        $rawEnd = $previousEnd;
        $nextStart = isset($cleanedTranscript[$lastIndex + 1])
            ? ($cleanedTranscript[$lastIndex + 1]['start'] ?? $rawEnd)
            : null;
        $segmentEnd = $nextStart !== null ? min($rawEnd, $nextStart) : $rawEnd;
        $outDuration = max(0, $segmentEnd - $segmentStart);

        $segments[] = [
            'text' => $segmentText,
            'start_time' => $segmentStart,
            'end_time' => $segmentEnd,
            'duration' => $outDuration,
            'entries' => $currentSegment
        ];
    }

    /**
     * Merge segments into complete sentences to improve translation quality
     * Combines segments until a complete sentence is found
     * 
     * @param array $segments YouTube segments with 'text', 'start', 'duration' keys
     * @return array Merged segments with complete sentences
     */
    public function mergeSegmentsIntoSentences(array $segments): array
    {
        $mergedSegments = [];
        $currentIndex = 0;
        $textBuffer = '';
        $startTime = null;
        $duration = 0;

        foreach ($segments as $index => $segment) {
            if (empty($textBuffer)) {
                $startTime = $segment['start'] ?? $segment['start_time'] ?? 0;
                $currentIndex = $index;
            }

            $text = $segment['text'] ?? '';
            $textBuffer .= ($textBuffer ? ' ' : '') . trim($text);
            $duration += $segment['duration'] ?? 0;

            // Check if segment ends with sentence-ending punctuation
            $endsWithSentencePunctuation = preg_match('/[.!?]+\s*$/', trim($textBuffer));

            // Also check for common phrase endings (like transitions)
            $hasTransitionEnd = preg_match('/(right|okay|now|so|well|actually)\s*[.!?]+\s*$/i', trim($textBuffer));

            // Merge into complete sentences if:
            // 1. Text ends with sentence punctuation (. ! ?)
            // 2. Text is reasonably long (30+ words)
            // 3. We've merged at least 2 segments or reached last segment
            $wordCount = str_word_count($textBuffer);
            $shouldCreateSegment = $endsWithSentencePunctuation ||
                ($wordCount >= 30 && ($index - $currentIndex >= 1 || $index === count($segments) - 1)) ||
                ($index === count($segments) - 1);

            if ($shouldCreateSegment && !empty($textBuffer)) {
                $mergedSegments[] = [
                    'index' => count($mergedSegments),
                    'text' => trim($textBuffer),
                    'original_text' => trim($textBuffer),
                    'start' => $startTime,
                    'start_time' => $startTime,
                    'end_time' => $startTime + $duration,
                    'duration' => $duration
                ];

                // Reset for next segment
                $textBuffer = '';
                $duration = 0;
            }
        }

        // Add any remaining text as final segment
        if (!empty(trim($textBuffer))) {
            $mergedSegments[] = [
                'index' => count($mergedSegments),
                'text' => trim($textBuffer),
                'original_text' => trim($textBuffer),
                'start' => $startTime,
                'start_time' => $startTime,
                'end_time' => $startTime + $duration,
                'duration' => $duration
            ];
        }

        return $mergedSegments;
    }
}

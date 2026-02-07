<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LipsyncSegmentManager
{
    /**
     * Split audio description into segments for lip-sync optimization
     * 
     * Strategy: Use D-ID for intro, middle checkpoints, and outro only (max 60s total)
     * Fill the gaps with media (images/videos) to create engaging content
     * 
     * @param string $audioPath Path to full audio file
     * @param float $totalDuration Total audio duration in seconds
     * @param int $maxLipsyncBudget Maximum seconds for D-ID lip-sync (default 60s)
     * @return array Segments configuration
     */
    public function planSegments($audioPath, $totalDuration, $maxLipsyncBudget = 60)
    {
        Log::info("Planning lip-sync segments", [
            'total_duration' => $totalDuration,
            'max_budget' => $maxLipsyncBudget
        ]);

        $segments = [];

        // Calculate segment durations
        if ($totalDuration <= $maxLipsyncBudget) {
            // Short content - all lip-sync
            $segments[] = [
                'type' => 'lipsync',
                'start' => 0,
                'duration' => $totalDuration,
                'label' => 'full'
            ];
        } elseif ($totalDuration <= 120) {
            // Medium content (60-120s) - intro, middle, outro
            $intro = 15;
            $outro = 15;
            $middle = min(10, $maxLipsyncBudget - $intro - $outro);
            $mediaGap = ($totalDuration - $intro - $middle - $outro) / 2;

            $segments = [
                ['type' => 'lipsync', 'start' => 0, 'duration' => $intro, 'label' => 'intro'],
                ['type' => 'media', 'start' => $intro, 'duration' => $mediaGap, 'label' => 'media_1'],
                ['type' => 'lipsync', 'start' => $intro + $mediaGap, 'duration' => $middle, 'label' => 'middle'],
                ['type' => 'media', 'start' => $intro + $mediaGap + $middle, 'duration' => $mediaGap, 'label' => 'media_2'],
                ['type' => 'lipsync', 'start' => $totalDuration - $outro, 'duration' => $outro, 'label' => 'outro'],
            ];
        } else {
            // Long content (>120s) - strategic checkpoints
            $intro = 20;
            $outro = 20;
            $middleBudget = $maxLipsyncBudget - $intro - $outro; // 20s for middle segments

            // Number of middle checkpoints
            $numMiddle = max(1, floor($middleBudget / 10));
            $middleSegDuration = floor($middleBudget / $numMiddle);

            // Calculate total media time
            $totalLipsync = $intro + ($middleSegDuration * $numMiddle) + $outro;
            $totalMedia = $totalDuration - $totalLipsync;
            $mediaSegDuration = $totalMedia / ($numMiddle + 1);

            // Build segments
            $currentTime = 0;

            // Intro
            $segments[] = ['type' => 'lipsync', 'start' => $currentTime, 'duration' => $intro, 'label' => 'intro'];
            $currentTime += $intro;

            // Alternating media and middle lip-syncs
            for ($i = 0; $i < $numMiddle; $i++) {
                $segments[] = ['type' => 'media', 'start' => $currentTime, 'duration' => $mediaSegDuration, 'label' => "media_" . ($i + 1)];
                $currentTime += $mediaSegDuration;

                $segments[] = ['type' => 'lipsync', 'start' => $currentTime, 'duration' => $middleSegDuration, 'label' => "middle_" . ($i + 1)];
                $currentTime += $middleSegDuration;
            }

            // Final media before outro
            $segments[] = ['type' => 'media', 'start' => $currentTime, 'duration' => $mediaSegDuration, 'label' => 'media_final'];
            $currentTime += $mediaSegDuration;

            // Outro
            $segments[] = ['type' => 'lipsync', 'start' => $currentTime, 'duration' => $outro, 'label' => 'outro'];
        }

        Log::info("Segment plan created", [
            'total_segments' => count($segments),
            'lipsync_count' => count(array_filter($segments, fn($s) => $s['type'] === 'lipsync')),
            'media_count' => count(array_filter($segments, fn($s) => $s['type'] === 'media')),
            'segments' => $segments
        ]);

        return $segments;
    }

    /**
     * Extract audio segment using FFmpeg
     * 
     * @param string $inputPath Input audio file
     * @param float $start Start time in seconds
     * @param float $duration Duration in seconds
     * @param string $outputPath Output audio file
     * @return bool Success
     */
    public function extractAudioSegment($inputPath, $start, $duration, $outputPath)
    {
        $ffmpegPath = env('FFMPEG_PATH', 'ffmpeg');

        $command = sprintf(
            '%s -i %s -ss %.2f -t %.2f -acodec copy %s -y 2>&1',
            $ffmpegPath,
            escapeshellarg($inputPath),
            $start,
            $duration,
            escapeshellarg($outputPath)
        );

        Log::info("Extracting audio segment", [
            'start' => $start,
            'duration' => $duration,
            'output' => $outputPath
        ]);

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            Log::error("Audio segment extraction failed", [
                'command' => $command,
                'output' => implode("\n", $output)
            ]);
            return false;
        }

        return true;
    }

    /**
     * Calculate total lip-sync duration from segments
     */
    public function calculateLipsyncDuration(array $segments): float
    {
        $total = 0;
        foreach ($segments as $segment) {
            if ($segment['type'] === 'lipsync') {
                $total += $segment['duration'];
            }
        }
        return $total;
    }
}

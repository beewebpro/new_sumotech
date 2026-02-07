<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Exception;

class AudioAlignmentService
{
    /**
     * Align audio segments with original timestamps
     * Apply time-stretching if needed to fit duration
     * 
     * @param array $audioSegments
     * @return array
     */
    public function alignSegments(array $audioSegments): array
    {
        $alignedSegments = [];
        $ttsService = app(TTSService::class);

        foreach ($audioSegments as $segment) {
            $audioPath = $segment['audio_path'];
            $targetDuration = $segment['duration'];

            // Get actual audio duration
            $actualDuration = $ttsService->getAudioDuration($audioPath);

            // Calculate speed adjustment needed
            $speedRatio = $actualDuration / $targetDuration;

            // If speed adjustment needed (tolerance: 10%)
            if (abs($speedRatio - 1.0) > 0.1) {
                // Apply time-stretching using ffmpeg or similar
                $adjustedPath = $this->timeStretchAudio($audioPath, $speedRatio);
                $segment['audio_path'] = $adjustedPath;
                $segment['adjusted'] = true;
                $segment['speed_ratio'] = $speedRatio;
            } else {
                $segment['adjusted'] = false;
                $segment['speed_ratio'] = 1.0;
            }

            $segment['actual_duration'] = $actualDuration;
            $alignedSegments[] = $segment;
        }

        return $alignedSegments;
    }

    /**
     * Time-stretch audio to match target duration
     * 
     * @param string $inputPath
     * @param float $speedRatio
     * @return string Path to adjusted audio
     */
    private function timeStretchAudio(string $inputPath, float $speedRatio): string
    {
        try {
            // Handle path that may or may not include storage prefix
            // If inputPath is like "public/dubsync/tts/xxx.wav", we need to resolve it properly
            $inputFullPath = $inputPath;

            // If path is relative and doesn't start with /, try to find it
            if (!file_exists($inputFullPath)) {
                // Try with storage path
                $inputFullPath = Storage::path($inputPath);
            }

            // If still not found, try without 'public/' prefix if it exists
            if (!file_exists($inputFullPath) && strpos($inputPath, 'public/') === 0) {
                $pathWithoutPublic = substr($inputPath, 7); // Remove 'public/'
                $inputFullPath = Storage::path('public/' . $pathWithoutPublic);
            }

            if (!file_exists($inputFullPath)) {
                \Log::error('Input audio file not found', [
                    'original_path' => $inputPath,
                    'resolved_path' => $inputFullPath
                ]);
                return $inputPath; // Return original if file not found
            }

            // Detect file extension
            $ext = pathinfo($inputPath, PATHINFO_EXTENSION) ?: 'mp3';

            // Create output path by replacing file extension
            // If inputPath is "public/dubsync/tts/segment_0_xxx.wav"
            // outputPath should be "public/dubsync/tts/segment_0_xxx_aligned.wav"
            $outputPath = preg_replace('/\.' . $ext . '$/', '_aligned.' . $ext, $inputPath);
            $outputFullPath = Storage::path($outputPath);

            // Ensure output directory exists
            $outputDir = dirname($outputFullPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Using ffmpeg atempo filter for time-stretching
            // atempo range: 0.5 to 2.0
            // speedRatio = actualDuration / targetDuration
            // If speedRatio > 1: audio is too long, need to speed up (tempo > 1)
            // If speedRatio < 1: audio is too short, need to slow down (tempo < 1)
            // Therefore: tempo = speedRatio (not 1.0 / speedRatio)
            $tempo = $speedRatio;

            // Clamp tempo to valid range
            $tempo = max(0.5, min(2.0, $tempo));

            $command = "ffmpeg -i \"{$inputFullPath}\" -filter:a \"atempo={$tempo}\" -y \"{$outputFullPath}\" 2>&1";

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($outputFullPath)) {
                \Log::info('Audio time-stretched successfully', [
                    'input' => $inputPath,
                    'output' => $outputPath,
                    'tempo' => $tempo,
                    'speed_ratio' => $speedRatio
                ]);
                return $outputPath;
            }

            \Log::warning('FFmpeg failed to time-stretch audio', [
                'input' => $inputPath,
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);

            // If ffmpeg fails, return original
            return $inputPath;
        } catch (Exception $e) {
            \Log::error('Time-stretch audio error', [
                'error' => $e->getMessage(),
                'input' => $inputPath
            ]);
            // Return original path if adjustment fails
            return $inputPath;
        }
    }
}

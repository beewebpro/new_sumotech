<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoCompositionService
{
    private $ffmpegPath;
    private $ffprobePath;

    public function __construct()
    {
        $this->ffmpegPath = env('FFMPEG_PATH', 'ffmpeg');
        $this->ffprobePath = env('FFPROBE_PATH', 'ffprobe');
    }

    /**
     * Compose final video from segments with music and transitions
     * 
     * @param array $segments Segment data with videos/images
     * @param array $options Composition options (intro_music, outro_music, bg_music, etc.)
     * @param string $outputPath Output video path
     * @return array Result with video path and duration
     */
    public function composeVideo(array $segments, array $options, $outputPath)
    {
        Log::info("Starting video composition", [
            'segments' => count($segments),
            'output' => $outputPath
        ]);

        $workDir = storage_path('app/temp/video_composition_' . time());
        if (!is_dir($workDir)) {
            mkdir($workDir, 0755, true);
        }

        try {
            // Step 1: Prepare all segment videos
            $preparedSegments = $this->prepareSegmentVideos($segments, $workDir, $options);

            // Step 2: Create transitions and concatenate
            $videoWithTransitions = $this->concatenateWithTransitions($preparedSegments, $workDir);

            // Step 3: Add background music (nhạc nền nhẹ)
            if (!empty($options['bg_music'])) {
                $videoWithBgMusic = $this->addBackgroundMusic($videoWithTransitions, $options['bg_music'], $workDir);
            } else {
                $videoWithBgMusic = $videoWithTransitions;
            }

            // Step 4: Add intro music
            if (!empty($options['intro_music'])) {
                $videoWithIntro = $this->addIntroMusic($videoWithBgMusic, $options['intro_music'], $workDir, $options);
            } else {
                $videoWithIntro = $videoWithBgMusic;
            }

            // Step 5: Add outro music
            if (!empty($options['outro_music'])) {
                $videoFinal = $this->addOutroMusic($videoWithIntro, $options['outro_music'], $workDir, $options);
            } else {
                $videoFinal = $videoWithIntro;
            }

            // Step 6: Move to final output
            if (file_exists($videoFinal)) {
                $outputDir = dirname($outputPath);
                if (!is_dir($outputDir)) {
                    mkdir($outputDir, 0755, true);
                }
                copy($videoFinal, $outputPath);
                $duration = $this->getVideoDuration($outputPath);

                Log::info("Video composition completed", [
                    'output' => $outputPath,
                    'duration' => $duration
                ]);

                return [
                    'success' => true,
                    'video_path' => $outputPath,
                    'duration' => $duration
                ];
            }

            throw new \Exception('Final video file not created');
        } finally {
            // Cleanup temp directory
            $this->cleanupDirectory($workDir);
        }
    }

    /**
     * Prepare segment videos (lip-sync videos and media with avatar overlay)
     */
    private function prepareSegmentVideos(array $segments, $workDir, $options)
    {
        $prepared = [];
        $avatarUrl = $options['avatar_url'] ?? null;

        foreach ($segments as $index => $segment) {
            if ($segment['type'] === 'lipsync') {
                // Lip-sync video is already prepared
                $prepared[] = [
                    'path' => $segment['video_path'],
                    'duration' => $segment['duration'],
                    'type' => 'lipsync'
                ];
            } else {
                // Media segment - convert image/video to video with avatar overlay
                $mediaVideo = $this->createMediaSegment(
                    $segment['media_path'],
                    $segment['duration'],
                    $avatarUrl,
                    $segment['audio_path'],
                    $workDir,
                    $index
                );
                $prepared[] = [
                    'path' => $mediaVideo,
                    'duration' => $segment['duration'],
                    'type' => 'media'
                ];
            }
        }

        return $prepared;
    }

    /**
     * Create media segment video with avatar overlay
     */
    private function createMediaSegment($mediaPath, $duration, $avatarUrl, $audioPath, $workDir, $index)
    {
        $outputVideo = $workDir . "/media_segment_{$index}.mp4";
        $isVideo = $this->isVideoFile($mediaPath);

        // Prepare avatar overlay (circular, 200x200px, top-right corner)
        $avatarFilter = '';
        if ($avatarUrl) {
            $avatarPath = $this->downloadAvatar($avatarUrl, $workDir, $index);
            if ($avatarPath) {
                // Create circular mask and overlay
                $avatarFilter = sprintf(
                    "[1:v]scale=200:200,format=rgba,geq=lum='p(X,Y)':a='if(lt(hypot(X-100,Y-100),100),255,0)'[avatar]; " .
                        "[base][avatar]overlay=W-220:20[outv]",
                    ''
                );
            }
        }

        if ($isVideo) {
            // Video media - loop if needed, add avatar overlay
            $command = sprintf(
                '%s -stream_loop -1 -i %s %s -i %s ' .
                    '-filter_complex "[0:v]scale=1920:1080:force_original_aspect_ratio=increase,crop=1920:1080[base]; %s" ' .
                    '-map "[outv]" -map 2:a -t %.2f -c:v libx264 -preset fast -crf 23 -c:a aac %s -y 2>&1',
                $this->ffmpegPath,
                escapeshellarg($mediaPath),
                $avatarUrl ? '-i ' . escapeshellarg($avatarPath) : '',
                escapeshellarg($audioPath),
                $avatarFilter ?: '[base]copy[outv]',
                $duration,
                escapeshellarg($outputVideo)
            );
        } else {
            // Image media - apply zoom effect
            $command = sprintf(
                '%s -loop 1 -i %s %s -i %s ' .
                    '-filter_complex "[0:v]scale=1920:1080:force_original_aspect_ratio=increase,crop=1920:1080,' .
                    'zoompan=z=\'min(zoom+0.0015,1.5)\':d=1:x=\'iw/2-(iw/zoom/2)\':y=\'ih/2-(ih/zoom/2)\':s=1920x1080:fps=30[base]; %s" ' .
                    '-map "[outv]" -map 2:a -t %.2f -c:v libx264 -preset fast -crf 23 -c:a aac %s -y 2>&1',
                $this->ffmpegPath,
                escapeshellarg($mediaPath),
                $avatarUrl ? '-i ' . escapeshellarg($avatarPath) : '',
                escapeshellarg($audioPath),
                $avatarFilter ?: '[base]copy[outv]',
                $duration,
                escapeshellarg($outputVideo)
            );
        }

        Log::info("Creating media segment", ['index' => $index, 'is_video' => $isVideo]);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputVideo)) {
            Log::error("Media segment creation failed", ['output' => implode("\n", $output)]);
            throw new \Exception("Failed to create media segment {$index}");
        }

        return $outputVideo;
    }

    /**
     * Concatenate segments with random transitions
     */
    private function concatenateWithTransitions(array $segments, $workDir)
    {
        $transitions = ['fade', 'wipeleft', 'wiperight', 'wipeup', 'wipedown', 'slideleft', 'slideright', 'dissolve'];
        $transitionDuration = 0.5; // 0.5s transition
        $targetResolution = '1920x1080'; // Target resolution for all segments

        // Build complex filter for transitions
        $filterParts = [];
        $inputs = '';

        foreach ($segments as $i => $segment) {
            $inputs .= " -i " . escapeshellarg($segment['path']);
        }

        // Scale all inputs to same resolution and fps first
        $scaledInputs = [];
        for ($i = 0; $i < count($segments); $i++) {
            $scaledInputs[] = "[{$i}:v]fps=30,scale={$targetResolution}:force_original_aspect_ratio=decrease,pad=1920:1080:(ow-iw)/2:(oh-ih)/2,setsar=1[v{$i}]";
        }

        // Create transition chain using scaled inputs
        $current = '[v0]';
        for ($i = 0; $i < count($segments) - 1; $i++) {
            $transition = $transitions[array_rand($transitions)];
            $next = $i + 1;
            $output = $i === count($segments) - 2 ? '[outv]' : "[vt{$next}]";

            $filterParts[] = sprintf(
                "%s[v%d]xfade=transition=%s:duration=%.2f:offset=%.2f%s",
                $current,
                $next,
                $transition,
                $transitionDuration,
                $this->calculateOffset($segments, $i, $transitionDuration),
                $output
            );

            $current = "[vt{$next}]";
        }

        // Audio mixing
        $audioMix = '';
        for ($i = 0; $i < count($segments); $i++) {
            $audioMix .= "[{$i}:a]";
        }
        $audioMix .= "concat=n=" . count($segments) . ":v=0:a=1[outa]";

        // Combine all filters: scale + transitions + audio
        $filterComplex = implode('; ', $scaledInputs) . '; ' . implode('; ', $filterParts) . '; ' . $audioMix;
        $outputVideo = $workDir . '/concatenated.mp4';

        $command = sprintf(
            '%s %s -filter_complex %s -map "[outv]" -map "[outa]" -c:v libx264 -preset fast -crf 23 -c:a aac %s -y 2>&1',
            $this->ffmpegPath,
            $inputs,
            escapeshellarg($filterComplex),
            escapeshellarg($outputVideo)
        );

        Log::info("Concatenating segments with transitions", [
            'segments_count' => count($segments),
            'target_resolution' => $targetResolution
        ]);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputVideo)) {
            Log::error("Concatenation failed", ['output' => implode("\n", $output)]);
            throw new \Exception("Failed to concatenate segments");
        }

        return $outputVideo;
    }

    /**
     * Add background music (nhạc nền) at low volume
     */
    private function addBackgroundMusic($videoPath, $bgMusicPath, $workDir)
    {
        $outputVideo = $workDir . '/with_bgmusic.mp4';
        $bgVolume = 0.15; // 15% volume for background music

        $command = sprintf(
            '%s -i %s -stream_loop -1 -i %s -filter_complex ' .
                '"[1:a]volume=%.2f[bg]; [0:a][bg]amix=inputs=2:duration=first:dropout_transition=2[outa]" ' .
                '-map 0:v -map "[outa]" -c:v copy -c:a aac -shortest %s -y 2>&1',
            $this->ffmpegPath,
            escapeshellarg($videoPath),
            escapeshellarg($bgMusicPath),
            $bgVolume,
            escapeshellarg($outputVideo)
        );

        Log::info("Adding background music");
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputVideo)) {
            Log::warning("Background music addition failed, continuing without it");
            return $videoPath;
        }

        return $outputVideo;
    }

    /**
     * Add intro music with fade effect
     */
    private function addIntroMusic($videoPath, $introMusicPath, $workDir, $options)
    {
        $outputVideo = $workDir . '/with_intro.mp4';
        $fadeDuration = $options['intro_fade_duration'] ?? 3;

        $command = sprintf(
            '%s -i %s -i %s -filter_complex ' .
                '"[1:a]afade=t=out:st=0:d=%.2f[intro]; [0:a][intro]concat=n=2:v=0:a=1[outa]" ' .
                '-map 0:v -map "[outa]" -c:v copy -c:a aac %s -y 2>&1',
            $this->ffmpegPath,
            escapeshellarg($videoPath),
            escapeshellarg($introMusicPath),
            $fadeDuration,
            escapeshellarg($outputVideo)
        );

        Log::info("Adding intro music");
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputVideo)) {
            Log::warning("Intro music addition failed, continuing without it");
            return $videoPath;
        }

        return $outputVideo;
    }

    /**
     * Add outro music
     */
    private function addOutroMusic($videoPath, $outroMusicPath, $workDir, $options)
    {
        $outputVideo = $workDir . '/final.mp4';
        $fadeDuration = $options['outro_fade_duration'] ?? 3;

        $command = sprintf(
            '%s -i %s -i %s -filter_complex ' .
                '"[0:a][1:a]concat=n=2:v=0:a=1[outa]" ' .
                '-map 0:v -map "[outa]" -c:v copy -c:a aac %s -y 2>&1',
            $this->ffmpegPath,
            escapeshellarg($videoPath),
            escapeshellarg($outroMusicPath),
            escapeshellarg($outputVideo)
        );

        Log::info("Adding outro music");
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputVideo)) {
            Log::warning("Outro music addition failed, continuing without it");
            return $videoPath;
        }

        return $outputVideo;
    }

    // Helper methods

    private function isVideoFile($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'avi', 'mov', 'mkv', 'webm']);
    }

    private function downloadAvatar($url, $workDir, $index)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return storage_path('app/public/' . $url);
        }

        $avatarPath = $workDir . "/avatar_{$index}.jpg";
        try {
            file_put_contents($avatarPath, file_get_contents($url));
            return $avatarPath;
        } catch (\Exception $e) {
            Log::error("Avatar download failed", ['url' => $url]);
            return null;
        }
    }

    private function calculateOffset($segments, $index, $transitionDuration)
    {
        $offset = 0;
        for ($i = 0; $i <= $index; $i++) {
            $offset += $segments[$i]['duration'];
        }
        return $offset - $transitionDuration;
    }

    private function getVideoDuration($videoPath)
    {
        $command = sprintf(
            '%s -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s',
            $this->ffprobePath,
            escapeshellarg($videoPath)
        );

        exec($command, $output);
        return !empty($output) ? (float)$output[0] : 0;
    }

    /**
     * Create a slideshow video from scene images synced to an audio track.
     * Each scene gets duration proportional to its description text length.
     * 
     * @param array $scenes Array of ['image_path' => string, 'description' => string, 'title' => string]
     * @param string $audioPath Path to the description audio file
     * @param float $audioDuration Total audio duration in seconds
     * @param string $outputPath Output video path
     * @return array Result with video path and duration
     */
    public function createSceneSlideshow(array $scenes, string $audioPath, float $audioDuration, string $outputPath): array
    {
        Log::info("Creating scene slideshow video", [
            'scenes' => count($scenes),
            'audio_duration' => $audioDuration,
            'output' => $outputPath
        ]);

        if (empty($scenes)) {
            throw new \Exception('Không có phân cảnh nào để tạo video.');
        }

        $workDir = storage_path('app/temp/scene_slideshow_' . time());
        if (!is_dir($workDir)) {
            mkdir($workDir, 0755, true);
        }

        try {
            // Step 1: Calculate duration for each scene proportionally
            $sceneDurations = $this->calculateSceneDurations($scenes, $audioDuration);

            // Step 2: Create individual scene clips with zoompan effect
            $sceneClips = [];
            foreach ($scenes as $index => $scene) {
                $clipPath = $this->createSceneClip(
                    $scene['image_path'],
                    $sceneDurations[$index],
                    $workDir,
                    $index
                );
                $sceneClips[] = [
                    'path' => $clipPath,
                    'duration' => $sceneDurations[$index]
                ];
            }

            // Step 3: Concatenate clips with transitions
            if (count($sceneClips) === 1) {
                $concatenatedVideo = $sceneClips[0]['path'];
            } else {
                $concatenatedVideo = $this->concatenateSceneClips($sceneClips, $workDir);
            }

            // Step 4: Add the description audio track
            $finalVideo = $this->addAudioToSlideshow($concatenatedVideo, $audioPath, $workDir);

            // Step 5: Move to final output
            $outputDir = dirname($outputPath);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            if (file_exists($finalVideo)) {
                copy($finalVideo, $outputPath);
                $duration = $this->getVideoDuration($outputPath);

                Log::info("Scene slideshow video completed", [
                    'output' => $outputPath,
                    'duration' => $duration,
                    'scenes' => count($scenes)
                ]);

                return [
                    'success' => true,
                    'video_path' => $outputPath,
                    'duration' => $duration
                ];
            }

            throw new \Exception('Final slideshow video file was not created.');
        } finally {
            $this->cleanupDirectory($workDir);
        }
    }

    /**
     * Calculate duration for each scene proportional to its description text length.
     * Ensures minimum 3 seconds per scene and accounts for transition time.
     */
    private function calculateSceneDurations(array $scenes, float $totalDuration): array
    {
        $transitionTime = 0.5; // seconds per transition
        $totalTransitions = max(0, count($scenes) - 1);
        $availableDuration = $totalDuration - ($totalTransitions * $transitionTime);
        $minDuration = 3.0; // minimum 3 seconds per scene

        // Calculate text lengths
        $textLengths = [];
        $totalLength = 0;
        foreach ($scenes as $scene) {
            $len = mb_strlen($scene['description'] ?? $scene['title'] ?? 'scene');
            $len = max($len, 1); // avoid zero
            $textLengths[] = $len;
            $totalLength += $len;
        }

        // Calculate proportional durations
        $durations = [];
        $totalAssigned = 0;
        foreach ($textLengths as $i => $len) {
            $proportion = $len / $totalLength;
            $duration = max($minDuration, $availableDuration * $proportion);
            $durations[] = round($duration, 2);
            $totalAssigned += $duration;
        }

        // Normalize to fit exact available duration
        if ($totalAssigned > 0 && abs($totalAssigned - $availableDuration) > 0.1) {
            $scale = $availableDuration / $totalAssigned;
            foreach ($durations as &$d) {
                $d = max($minDuration, round($d * $scale, 2));
            }
        }

        Log::info("Scene durations calculated", [
            'durations' => $durations,
            'total' => array_sum($durations),
            'available' => $availableDuration
        ]);

        return $durations;
    }

    /**
     * Create a single scene clip with zoompan effect (no audio).
     */
    private function createSceneClip(string $imagePath, float $duration, string $workDir, int $index): string
    {
        $outputClip = $workDir . "/scene_clip_{$index}.mp4";

        // Zoompan: slow zoom in from 1.0x to 1.2x over the clip duration
        // d=duration*fps makes zoompan last exactly the clip duration
        $fps = 30;
        $totalFrames = (int)ceil($duration * $fps);
        $zoomSpeed = 0.2 / $totalFrames; // zoom from 1.0 to 1.2

        $command = sprintf(
            '%s -loop 1 -i %s -t %.2f -vf %s -c:v libx264 -preset fast -crf 23 -pix_fmt yuv420p %s -y 2>&1',
            $this->ffmpegPath,
            escapeshellarg($imagePath),
            $duration,
            escapeshellarg(
                "scale=1920:1080:force_original_aspect_ratio=increase,crop=1920:1080," .
                    "zoompan=z='min(zoom+{$zoomSpeed},1.2)':d=1" .
                    ":x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)'" .
                    ":s=1920x1080:fps={$fps}"
            ),
            escapeshellarg($outputClip)
        );

        Log::info("Creating scene clip", ['index' => $index, 'duration' => $duration]);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputClip)) {
            Log::error("Scene clip creation failed", [
                'index' => $index,
                'output' => implode("\n", $output)
            ]);
            throw new \Exception("Failed to create scene clip #{$index}");
        }

        return $outputClip;
    }

    /**
     * Concatenate scene clips with xfade transitions (video only, no audio).
     */
    private function concatenateSceneClips(array $clips, string $workDir): string
    {
        $transitions = ['fade', 'wipeleft', 'wiperight', 'wipeup', 'wipedown', 'slideleft', 'slideright', 'dissolve'];
        $transitionDuration = 0.5;

        $inputs = '';
        foreach ($clips as $clip) {
            $inputs .= ' -i ' . escapeshellarg($clip['path']);
        }

        // Build xfade chain
        $filterParts = [];
        $current = '[0:v]';
        for ($i = 0; $i < count($clips) - 1; $i++) {
            $transition = $transitions[array_rand($transitions)];
            $next = $i + 1;
            $output = ($i === count($clips) - 2) ? '[outv]' : "[vt{$next}]";

            // Calculate offset: sum of durations up to and including clip i, minus accumulated transitions
            $offset = 0;
            for ($j = 0; $j <= $i; $j++) {
                $offset += $clips[$j]['duration'];
            }
            $offset -= ($i + 1) * $transitionDuration; // Each previous transition eats transitionDuration
            $offset = max(0, $offset);

            $filterParts[] = sprintf(
                "%s[%d:v]xfade=transition=%s:duration=%.2f:offset=%.2f%s",
                $current,
                $next,
                $transition,
                $transitionDuration,
                $offset,
                $output
            );

            $current = "[vt{$next}]";
        }

        $filterComplex = implode('; ', $filterParts);
        $outputVideo = $workDir . '/concatenated_scenes.mp4';

        $command = sprintf(
            '%s %s -filter_complex %s -map "[outv]" -c:v libx264 -preset fast -crf 23 -pix_fmt yuv420p %s -y 2>&1',
            $this->ffmpegPath,
            $inputs,
            escapeshellarg($filterComplex),
            escapeshellarg($outputVideo)
        );

        Log::info("Concatenating scene clips with transitions", ['count' => count($clips)]);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputVideo)) {
            Log::error("Scene concatenation failed", ['output' => implode("\n", $output)]);

            // Fallback: use concat demuxer (no transitions)
            return $this->fallbackConcatenateClips($clips, $workDir);
        }

        return $outputVideo;
    }

    /**
     * Fallback concatenation using concat demuxer (no transitions).
     */
    private function fallbackConcatenateClips(array $clips, string $workDir): string
    {
        $listFile = $workDir . '/concat_list.txt';
        $content = '';
        foreach ($clips as $clip) {
            $content .= "file " . escapeshellarg($clip['path']) . "\n";
        }
        file_put_contents($listFile, $content);

        $outputVideo = $workDir . '/concatenated_scenes.mp4';

        $command = sprintf(
            '%s -f concat -safe 0 -i %s -c:v libx264 -preset fast -crf 23 -pix_fmt yuv420p %s -y 2>&1',
            $this->ffmpegPath,
            escapeshellarg($listFile),
            escapeshellarg($outputVideo)
        );

        Log::info("Fallback concatenation (no transitions)");
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputVideo)) {
            Log::error("Fallback concatenation also failed", ['output' => implode("\n", $output)]);
            throw new \Exception("Failed to concatenate scene clips");
        }

        return $outputVideo;
    }

    /**
     * Add audio track to the slideshow video.
     */
    private function addAudioToSlideshow(string $videoPath, string $audioPath, string $workDir): string
    {
        $outputVideo = $workDir . '/slideshow_with_audio.mp4';

        $command = sprintf(
            '%s -i %s -i %s -c:v copy -c:a aac -b:a 192k -shortest %s -y 2>&1',
            $this->ffmpegPath,
            escapeshellarg($videoPath),
            escapeshellarg($audioPath),
            escapeshellarg($outputVideo)
        );

        Log::info("Adding audio to slideshow");
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputVideo)) {
            Log::error("Adding audio failed", ['output' => implode("\n", $output)]);
            throw new \Exception("Failed to add audio to slideshow video");
        }

        return $outputVideo;
    }

    private function cleanupDirectory($dir)
    {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($dir);
        }
    }
}

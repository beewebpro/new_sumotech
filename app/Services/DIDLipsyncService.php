<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DIDLipsyncService
{
    private $apiKey;
    private $baseUrl = 'https://api.d-id.com';

    public function __construct()
    {
        $this->apiKey = env('DID_API_KEY');
    }

    /**
     * Generate lip-sync video from audio and image
     * 
     * @param string $audioPath Full path to audio file
     * @param string $imagePath Full path to image file or URL
     * @param array $options Additional options (optional)
     * @return array ['video_url' => string, 'video_id' => string]
     * @throws \Exception
     */
    public function generateVideo($audioPath, $imagePath, $options = [])
    {
        if (!$this->apiKey) {
            throw new \Exception('D-ID API key not configured. Please set DID_API_KEY in .env file.');
        }

        // Upload audio to D-ID if it's a local file
        $audioUrl = $this->uploadAudio($audioPath);

        // Upload image if it's a local file, otherwise use URL directly
        $imageUrl = filter_var($imagePath, FILTER_VALIDATE_URL)
            ? $imagePath
            : $this->uploadImage($imagePath);

        // Create talk (lip-sync) request
        $talkId = $this->createTalk($imageUrl, $audioUrl, $options);

        // Poll for completion
        $result = $this->waitForCompletion($talkId);

        return [
            'video_url' => $result['result_url'],
            'video_id' => $talkId,
            'duration' => $result['duration'] ?? null
        ];
    }

    /**
     * Upload audio file to D-ID
     */
    private function uploadAudio($audioPath)
    {
        Log::info("Uploading audio to D-ID: {$audioPath}");

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->apiKey,
        ])->attach(
            'audio',
            file_get_contents($audioPath),
            basename($audioPath)
        )->post("{$this->baseUrl}/audios");

        if (!$response->successful()) {
            throw new \Exception('Failed to upload audio to D-ID: ' . $response->body());
        }

        $data = $response->json();
        Log::info("Audio uploaded successfully", ['url' => $data['url']]);

        return $data['url'];
    }

    /**
     * Upload image file to D-ID
     */
    private function uploadImage($imagePath)
    {
        Log::info("Uploading image to D-ID: {$imagePath}");

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->apiKey,
        ])->attach(
            'image',
            file_get_contents($imagePath),
            basename($imagePath)
        )->post("{$this->baseUrl}/images");

        if (!$response->successful()) {
            throw new \Exception('Failed to upload image to D-ID: ' . $response->body());
        }

        $data = $response->json();
        Log::info("Image uploaded successfully", ['url' => $data['url']]);

        return $data['url'];
    }

    /**
     * Create a talk (lip-sync) request
     */
    private function createTalk($imageUrl, $audioUrl, $options = [])
    {
        Log::info("Creating D-ID talk", [
            'image_url' => $imageUrl,
            'audio_url' => $audioUrl
        ]);

        $payload = [
            'source_url' => $imageUrl,
            'driver_url' => 'bank://lively', // Default driver for natural movement
            'script' => [
                'type' => 'audio',
                'audio_url' => $audioUrl,
            ],
            'config' => [
                'stitch' => true, // Smoothly stitch the animated result
                'result_format' => 'mp4',
            ]
        ];

        // Merge custom options
        if (isset($options['driver_url'])) {
            $payload['driver_url'] = $options['driver_url'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/talks", $payload);

        if (!$response->successful()) {
            throw new \Exception('Failed to create D-ID talk: ' . $response->body());
        }

        $data = $response->json();
        $talkId = $data['id'];

        Log::info("D-ID talk created successfully", ['talk_id' => $talkId]);

        return $talkId;
    }

    /**
     * Wait for video generation to complete
     */
    private function waitForCompletion($talkId, $maxAttempts = 60, $pollInterval = 5)
    {
        Log::info("Waiting for D-ID video completion", ['talk_id' => $talkId]);

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey,
            ])->get("{$this->baseUrl}/talks/{$talkId}");

            if (!$response->successful()) {
                throw new \Exception('Failed to check D-ID talk status: ' . $response->body());
            }

            $data = $response->json();
            $status = $data['status'];

            Log::info("D-ID talk status", [
                'talk_id' => $talkId,
                'status' => $status,
                'attempt' => $i + 1
            ]);

            if ($status === 'done') {
                Log::info("D-ID video completed", [
                    'result_url' => $data['result_url'],
                    'duration' => $data['duration'] ?? null
                ]);

                return $data;
            }

            if ($status === 'error' || $status === 'rejected') {
                throw new \Exception('D-ID video generation failed: ' . ($data['error']['message'] ?? 'Unknown error'));
            }

            // Still processing, wait before next poll
            sleep($pollInterval);
        }

        throw new \Exception('D-ID video generation timeout after ' . ($maxAttempts * $pollInterval) . ' seconds');
    }

    /**
     * Download video from D-ID and save to local storage
     * 
     * @param string $videoUrl D-ID video URL
     * @param string $savePath Local path to save video (relative to storage/app/public)
     * @return string Relative path to saved video
     */
    public function downloadVideo($videoUrl, $savePath)
    {
        Log::info("Downloading D-ID video", ['url' => $videoUrl, 'path' => $savePath]);

        // Ensure directory exists
        $directory = dirname($savePath);
        $fullDirectory = storage_path('app/public/' . $directory);
        if (!is_dir($fullDirectory)) {
            mkdir($fullDirectory, 0755, true);
        }

        // Download video
        $videoContent = file_get_contents($videoUrl);
        if ($videoContent === false) {
            throw new \Exception('Failed to download video from D-ID');
        }

        // Save to storage
        $fullPath = storage_path('app/public/' . $savePath);
        file_put_contents($fullPath, $videoContent);

        Log::info("D-ID video downloaded successfully", ['path' => $savePath]);

        return $savePath;
    }

    /**
     * Get available driver styles for lip-sync animation
     * 
     * @return array List of driver URLs and their descriptions
     */
    public function getDrivers()
    {
        return [
            'bank://lively' => 'Natural and lively movement (recommended)',
            'bank://subtle' => 'Subtle and minimal movement',
            'bank://expressive' => 'Expressive and animated',
            'bank://serious' => 'Serious and professional tone',
        ];
    }

    /**
     * Delete a talk from D-ID
     */
    public function deleteTalk($talkId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->apiKey,
        ])->delete("{$this->baseUrl}/talks/{$talkId}");

        return $response->successful();
    }
}

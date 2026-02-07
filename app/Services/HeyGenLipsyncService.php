<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HeyGenLipsyncService
{
    private $apiKey;
    private $baseUrl;
    private $createEndpoint;
    private $statusEndpoint;

    public function __construct()
    {
        $this->apiKey = env('HEYGEN_API_KEY');
        $this->baseUrl = env('HEYGEN_BASE_URL', 'https://api.heygen.com');
        $this->createEndpoint = env('HEYGEN_CREATE_ENDPOINT', '/v1/video.generate');
        $this->statusEndpoint = env('HEYGEN_STATUS_ENDPOINT', '/v1/video.status');
    }

    /**
     * Generate lip-sync video from audio and image using HeyGen
     *
     * @param string $audioPath Full path to audio file
     * @param string $imagePath Full path to image file or URL
     * @param array $options Additional options
     * @return array ['video_url' => string, 'video_id' => string]
     */
    public function generateVideo($audioPath, $imagePath, $options = [])
    {
        if (!$this->apiKey) {
            throw new \Exception('HeyGen API key not configured. Please set HEYGEN_API_KEY in .env file.');
        }

        $audioUrl = $this->resolvePublicUrl($audioPath, 'audio');
        $imageUrl = $this->resolvePublicUrl($imagePath, 'image');

        $payload = $this->buildPayload($audioUrl, $imageUrl, $options);

        Log::info('Creating HeyGen video', [
            'endpoint' => $this->baseUrl . $this->createEndpoint,
        ]);

        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . $this->createEndpoint, $payload);

        if (!$response->successful()) {
            throw new \Exception('Failed to create HeyGen video: ' . $response->body());
        }

        $data = $response->json();
        $videoId = $data['data']['video_id'] ?? $data['video_id'] ?? null;

        if (!$videoId) {
            throw new \Exception('HeyGen response missing video_id: ' . $response->body());
        }

        $result = $this->waitForCompletion($videoId);

        return [
            'video_url' => $result['video_url'],
            'video_id' => $videoId,
            'duration' => $result['duration'] ?? null
        ];
    }

    /**
     * Download video from HeyGen and save to local storage
     */
    public function downloadVideo($videoUrl, $savePath)
    {
        Log::info('Downloading HeyGen video', ['url' => $videoUrl, 'path' => $savePath]);

        $directory = dirname($savePath);
        $fullDirectory = storage_path('app/public/' . $directory);
        if (!is_dir($fullDirectory)) {
            mkdir($fullDirectory, 0755, true);
        }

        $videoContent = file_get_contents($videoUrl);
        if ($videoContent === false) {
            throw new \Exception('Failed to download video from HeyGen');
        }

        $fullPath = storage_path('app/public/' . $savePath);
        file_put_contents($fullPath, $videoContent);

        return $savePath;
    }

    private function buildPayload($audioUrl, $imageUrl, $options)
    {
        $characterType = $options['character_type'] ?? 'talking_photo';
        $dimension = [
            'width' => $options['width'] ?? 1280,
            'height' => $options['height'] ?? 720,
        ];

        $character = [
            'type' => $characterType,
        ];

        if ($characterType === 'talking_photo') {
            $character['talking_photo_url'] = $imageUrl;
        }

        if (!empty($options['avatar_id'])) {
            $character['avatar_id'] = $options['avatar_id'];
        }

        $videoInput = [
            'character' => $character,
            'voice' => [
                'type' => 'audio',
                'audio_url' => $audioUrl,
            ],
        ];

        $payload = [
            'video_inputs' => [$videoInput],
            'dimension' => $dimension,
        ];

        if (isset($options['test'])) {
            $payload['test'] = (bool) $options['test'];
        }

        return $payload;
    }

    private function waitForCompletion($videoId, $maxAttempts = 60, $pollInterval = 5)
    {
        Log::info('Waiting for HeyGen video completion', ['video_id' => $videoId]);

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
            ])->get($this->baseUrl . $this->statusEndpoint, [
                'video_id' => $videoId,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to check HeyGen status: ' . $response->body());
            }

            $data = $response->json();
            $status = $data['data']['status'] ?? $data['status'] ?? null;

            Log::info('HeyGen status', [
                'video_id' => $videoId,
                'status' => $status,
                'attempt' => $i + 1,
            ]);

            if ($status === 'completed' || $status === 'done') {
                $videoUrl = $data['data']['video_url'] ?? $data['video_url'] ?? null;
                if (!$videoUrl) {
                    throw new \Exception('HeyGen completed but missing video_url');
                }

                return [
                    'video_url' => $videoUrl,
                    'duration' => $data['data']['duration'] ?? $data['duration'] ?? null,
                ];
            }

            if ($status === 'failed' || $status === 'error') {
                $error = $data['data']['error'] ?? $data['error'] ?? 'Unknown error';
                throw new \Exception('HeyGen video generation failed: ' . $error);
            }

            sleep($pollInterval);
        }

        throw new \Exception('HeyGen video generation timeout after ' . ($maxAttempts * $pollInterval) . ' seconds');
    }

    private function resolvePublicUrl($pathOrUrl, $type)
    {
        if (filter_var($pathOrUrl, FILTER_VALIDATE_URL)) {
            return $pathOrUrl;
        }

        $publicRoot = storage_path('app/public/');
        if (str_starts_with($pathOrUrl, $publicRoot)) {
            $relative = ltrim(str_replace($publicRoot, '', $pathOrUrl), DIRECTORY_SEPARATOR);
            return rtrim(config('app.url'), '/') . Storage::url($relative);
        }

        $normalized = ltrim($pathOrUrl, '/');
        if (Storage::disk('public')->exists($normalized)) {
            return rtrim(config('app.url'), '/') . Storage::url($normalized);
        }

        throw new \Exception("{$type} file is not publicly accessible: {$pathOrUrl}");
    }
}

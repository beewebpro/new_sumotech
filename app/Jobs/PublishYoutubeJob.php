<?php

namespace App\Jobs;

use App\Http\Controllers\AudioBookController;
use App\Models\AudioBook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PublishYoutubeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $audioBookId;
    public string $mode;
    public array $payload;
    public $timeout = 14400; // 4 hours

    public function __construct(int $audioBookId, string $mode, array $payload)
    {
        $this->audioBookId = $audioBookId;
        $this->mode = $mode;
        $this->payload = $payload;
    }

    public function handle(): void
    {
        $audioBook = AudioBook::find($this->audioBookId);
        if (!$audioBook) {
            $this->updateProgress([
                'status' => 'error',
                'percent' => 100,
                'message' => 'Khong tim thay audiobook.'
            ]);
            return;
        }

        $this->updateProgress([
            'status' => 'processing',
            'percent' => 5,
            'message' => $this->getStartMessage(),
        ]);

        try {
            $controller = app(AudioBookController::class);
            $request = new Request($this->payload);

            if ($this->mode === 'upload') {
                $response = $controller->uploadToYoutube($request, $audioBook);
            } elseif ($this->mode === 'create_playlist') {
                $response = $controller->createPlaylistAndUpload($request, $audioBook);
            } elseif ($this->mode === 'add_to_playlist') {
                $response = $controller->addToExistingPlaylist($request, $audioBook);
            } else {
                $this->updateProgress([
                    'status' => 'error',
                    'percent' => 100,
                    'message' => 'Che do phat hanh khong hop le.'
                ]);
                return;
            }

            $data = method_exists($response, 'getData') ? $response->getData(true) : null;
            $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 500;

            if (!$data || $status >= 400 || !($data['success'] ?? false)) {
                $this->updateProgress([
                    'status' => 'error',
                    'percent' => 100,
                    'message' => $data['error'] ?? 'Upload that bai.',
                    'result' => $data,
                ]);
                return;
            }

            $this->updateProgress([
                'status' => 'completed',
                'percent' => 100,
                'message' => 'Hoan tat.',
                'result' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('PublishYoutubeJob failed', [
                'error' => $e->getMessage(),
                'audiobook' => $this->audioBookId,
                'mode' => $this->mode,
            ]);

            $this->updateProgress([
                'status' => 'error',
                'percent' => 100,
                'message' => 'Loi: ' . $e->getMessage(),
            ]);
        }
    }

    private function getStartMessage(): string
    {
        if ($this->mode === 'upload') {
            return 'Dang upload video...';
        }
        if ($this->mode === 'add_to_playlist') {
            return 'Dang upload vao playlist...';
        }
        return 'Dang tao playlist va upload...';
    }

    private function updateProgress(array $data): void
    {
        $payload = array_merge([
            'status' => 'processing',
            'percent' => 0,
            'message' => '',
            'result' => null,
            'updated_at' => now()->toIso8601String(),
        ], $data);

        Cache::put("publish_progress_{$this->audioBookId}", $payload, now()->addHours(6));
    }
}

<?php

namespace App\Jobs;

use App\Models\DubSyncProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessAISegmentation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $projectId;
    protected $cleanedTranscript;

    public function __construct($projectId, $cleanedTranscript)
    {
        $this->projectId = $projectId;
        $this->cleanedTranscript = $cleanedTranscript;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::info('ProcessAISegmentation: Starting for project', ['project_id' => $this->projectId]);

            // Update progress
            $this->setProgress('processing', 10, 'Đang xử lý AI segmentation...');

            // Process AI segmentation (may fallback to traditional if AI fails)
            $segments = app('App\Services\AISegmentationService')->segmentWithAI(
                $this->cleanedTranscript,
                $this->projectId
            );

            // Update project with segments
            $project = DubSyncProject::find($this->projectId);
            if ($project) {
                $project->update([
                    'segments' => $segments,
                    'status' => 'transcribed'
                ]);

                Log::info('ProcessAISegmentation: Completed for project', [
                    'project_id' => $this->projectId,
                    'segments_count' => count($segments)
                ]);

                // Check if AI actually failed and used fallback
                // AISegmentationService sets cache to 'error' status if it fell back
                $cachedProgress = Cache::get("ai_segmentation_progress_{$this->projectId}");
                if ($cachedProgress && $cachedProgress['status'] === 'error') {
                    // AI failed but fallback succeeded
                    $this->setProgress('completed', 100, 'Hoàn tất (sử dụng phương pháp thường)');
                } else {
                    // AI succeeded
                    $this->setProgress('completed', 100, 'Hoàn tất AI segmentation!');
                }
            } else {
                throw new \Exception('Project not found');
            }
        } catch (\Exception $e) {
            Log::error('ProcessAISegmentation: Error', [
                'project_id' => $this->projectId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Mark as error in cache
            $this->setProgress('error', 0, 'Lỗi: ' . $e->getMessage());

            // Update project status
            $project = DubSyncProject::find($this->projectId);
            if ($project) {
                $project->update([
                    'status' => 'error',
                    'error_message' => $e->getMessage()
                ]);
            }

            throw $e;
        }
    }

    /**
     * Set progress status
     */
    private function setProgress(string $status, int $percentage, string $message)
    {
        Cache::put("ai_segmentation_progress_{$this->projectId}", [
            'status' => $status,
            'percentage' => $percentage,
            'message' => $message,
            'timestamp' => now()->timestamp
        ], now()->addMinutes(30));
    }
}

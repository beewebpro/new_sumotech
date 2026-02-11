<?php

namespace App\Console\Commands;

use App\Models\AutomationLog;
use App\Services\AudiobookAutoProcessor;
use Illuminate\Console\Command;

class AutoProcessAudiobooks extends Command
{
    protected $signature = 'audiobook:auto-process
        {--limit=3 : Max audiobooks to process per run}
        {--dry-run : Show what would be processed without executing}
        {--tts-only : Only process TTS audio}
        {--video-only : Only process video generation}';

    protected $description = 'Auto-process audiobooks: generate TTS, create videos, send email notifications';

    public function handle(AudiobookAutoProcessor $processor): int
    {
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        $ttsOnly = $this->option('tts-only');
        $videoOnly = $this->option('video-only');

        // Build command string for logging
        $cmdParts = ['audiobook:auto-process', '--limit=' . $limit];
        if ($dryRun) $cmdParts[] = '--dry-run';
        if ($ttsOnly) $cmdParts[] = '--tts-only';
        if ($videoOnly) $cmdParts[] = '--video-only';
        $cmdString = implode(' ', $cmdParts);

        // Start automation log
        $log = AutomationLog::startLog($cmdString, $dryRun ? 'manual' : 'schedule');

        $this->info('🚀 Audiobook Auto-Processor started at ' . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        if ($dryRun) {
            $result = $this->handleDryRun($processor, $limit);
            $log->markSuccess('Dry run completed');
            return $result;
        }

        $totalProcessed = 0;
        $metaData = [];

        try {
            // Step 1: Process TTS
            if (!$videoOnly) {
                $this->info('📖 Step 1: Processing pending TTS...');
                $ttsResult = $processor->processPendingTTS($limit);

                $this->info("   Found: {$ttsResult['found']} audiobooks");
                $this->info("   Processed: " . count($ttsResult['processed']));
                $this->info("   Completed: " . count($ttsResult['completed']));

                if (!empty($ttsResult['completed'])) {
                    $this->info('   ✅ Email sent for completed: ' . implode(', ', $ttsResult['completed']));
                }

                $totalProcessed += count($ttsResult['processed']);
                $metaData['tts_found'] = $ttsResult['found'];
                $metaData['tts_processed'] = count($ttsResult['processed']);
                $metaData['tts_completed'] = count($ttsResult['completed']);
                $this->newLine();
            }

            // Step 2: Process Videos
            if (!$ttsOnly) {
                $this->info('🎬 Step 2: Processing pending videos...');
                $videoResult = $processor->processPendingVideos($limit);

                $this->info("   Found: {$videoResult['found']} audiobooks");
                $this->info("   Processed: " . count($videoResult['processed']));
                $this->info("   Completed: " . count($videoResult['completed']));

                if (!empty($videoResult['completed'])) {
                    $this->info('   ✅ Email sent for completed: ' . implode(', ', $videoResult['completed']));
                }

                $totalProcessed += count($videoResult['processed']);
                $metaData['video_found'] = $videoResult['found'];
                $metaData['video_processed'] = count($videoResult['processed']);
                $metaData['video_completed'] = count($videoResult['completed']);
                $this->newLine();
            }

            $metaData['total_processed'] = $totalProcessed;

            if ($totalProcessed === 0) {
                $summary = 'Nothing to process. All audiobooks are up to date.';
                $this->info('✨ ' . $summary);
            } else {
                $summary = "Processed {$totalProcessed} audiobooks.";
                $this->info("🎉 Done! {$summary}");
            }

            $this->info('⏱️  Finished at ' . now()->format('Y-m-d H:i:s'));

            $log->markSuccess($summary, $metaData);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $metaData['total_processed'] = $totalProcessed;
            $log->markFailed($e->getMessage(), null, $metaData);

            $this->error('❌ Command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function handleDryRun(AudiobookAutoProcessor $processor, int $limit): int
    {
        $this->warn('🔍 DRY RUN - No changes will be made');
        $this->newLine();

        $info = $processor->getDryRunInfo($limit);

        $this->info('📖 TTS Pending (' . count($info['tts_pending']) . '):');
        if (empty($info['tts_pending'])) {
            $this->line('   (none)');
        } else {
            foreach ($info['tts_pending'] as $book) {
                $this->line("   - [{$book['id']}] {$book['title']} (provider: {$book['tts_provider']})");
            }
        }

        $this->newLine();

        $this->info('🎬 Video Pending (' . count($info['video_pending']) . '):');
        if (empty($info['video_pending'])) {
            $this->line('   (none)');
        } else {
            foreach ($info['video_pending'] as $book) {
                $this->line("   - [{$book['id']}] {$book['title']}");
            }
        }

        return Command::SUCCESS;
    }
}

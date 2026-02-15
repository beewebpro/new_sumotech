<?php

namespace App\Console;

use App\Models\AutomationLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('audiobook:auto-process --limit=3')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/audiobook-auto-process.log'));

        $this->registerScheduleEventLogging($schedule);
    }

    private function registerScheduleEventLogging(Schedule $schedule): void
    {
        $skipCommands = [
            'audiobook:auto-process',
        ];

        foreach ($schedule->events() as $event) {
            $rawCommand = $event->command ?? $event->description ?? $event->getSummaryForDisplay();
            $cleanCommand = preg_replace("/^.*?'artisan'\s*/", '', (string) $rawCommand);
            $cleanCommand = trim((string) $cleanCommand, "' ");
            $commandName = explode(' ', trim($cleanCommand))[0] ?? $cleanCommand;

            if (in_array($commandName, $skipCommands, true)) {
                continue;
            }

            $cacheKey = 'automation_log:' . sha1($event->mutexName());

            $event->before(function () use ($cleanCommand, $cacheKey) {
                $log = AutomationLog::startLog($cleanCommand, 'schedule');
                Cache::put($cacheKey, $log->id, now()->addHours(6));
            });

            $event->after(function () use ($event, $cacheKey) {
                $logId = Cache::pull($cacheKey);
                if (!$logId) {
                    return;
                }

                $log = AutomationLog::find($logId);
                if (!$log) {
                    return;
                }

                $output = null;
                $outputFile = $event->output;
                if ($outputFile && is_file($outputFile)) {
                    $output = trim((string) file_get_contents($outputFile));
                    if ($output === '') {
                        $output = null;
                    }
                }

                if ((int) $event->exitCode === 0) {
                    $log->markSuccess($output);
                    return;
                }

                $log->markFailed('Exit code: ' . (int) $event->exitCode, $output);
            });
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AutomationLog;
use Illuminate\Http\Request;
use Illuminate\Console\Scheduling\Schedule;

class AutomationReportController extends Controller
{
    public function index(Request $request)
    {
        $query = AutomationLog::query();

        if ($request->filled('command_name')) {
            $query->where('command_name', $request->command_name);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('trigger')) {
            $query->where('trigger', $request->trigger);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('started_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('started_at', '<=', $request->date_to);
        }

        // Statistics from filtered query
        $totalRuns = (clone $query)->count();
        $successfulRuns = (clone $query)->where('status', 'success')->count();
        $failedRuns = (clone $query)->where('status', 'failed')->count();
        $runningRuns = (clone $query)->where('status', 'running')->count();

        $lastRun = AutomationLog::latest('started_at')->first();
        $avgDuration = AutomationLog::whereNotNull('duration_seconds')
            ->where('status', 'success')
            ->avg('duration_seconds');

        // Paginated history
        $perPage = in_array($request->get('per_page'), [10, 20, 30, 50, 100]) ? (int) $request->get('per_page') : 30;
        $logs = $query->latest('started_at')->paginate($perPage)->withQueryString();

        // Filter options
        $commandNames = AutomationLog::select('command_name')->distinct()->pluck('command_name');

        // Upcoming scheduled jobs
        $upcomingJobs = $this->getUpcomingScheduledJobs();

        return view('automation-reports.index', compact(
            'logs',
            'totalRuns',
            'successfulRuns',
            'failedRuns',
            'runningRuns',
            'lastRun',
            'avgDuration',
            'commandNames',
            'upcomingJobs'
        ));
    }

    private function getUpcomingScheduledJobs(): array
    {
        try {
            $schedule = app(Schedule::class);
            $events = $schedule->events();
            $jobs = [];

            foreach ($events as $event) {
                $command = $event->command ?? $event->description ?? 'Unknown';
                // Clean up command: remove PHP binary and artisan path
                $cleanCommand = preg_replace("/^.*?'artisan'\s*/", '', $command);
                $cleanCommand = trim($cleanCommand, "' ");

                $jobs[] = [
                    'command' => $cleanCommand ?: $command,
                    'expression' => $event->expression,
                    'readable' => $this->cronToReadable($event->expression),
                    'next_run' => $this->getNextRunDate($event->expression),
                    'without_overlapping' => $event->withoutOverlapping ?? false,
                    'run_in_background' => $event->runInBackground ?? false,
                ];
            }

            return $jobs;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getNextRunDate(string $expression): \Carbon\Carbon
    {
        $cron = new \Cron\CronExpression($expression);
        return \Carbon\Carbon::instance($cron->getNextRunDate());
    }

    private function cronToReadable(string $expression): string
    {
        $map = [
            '* * * * *' => 'Every minute',
            '*/5 * * * *' => 'Every 5 minutes',
            '*/10 * * * *' => 'Every 10 minutes',
            '*/15 * * * *' => 'Every 15 minutes',
            '*/30 * * * *' => 'Every 30 minutes',
            '0 * * * *' => 'Hourly',
            '0 */2 * * *' => 'Every 2 hours',
            '0 */3 * * *' => 'Every 3 hours',
            '0 */6 * * *' => 'Every 6 hours',
            '0 0 * * *' => 'Daily at midnight',
            '0 0 * * 0' => 'Weekly on Sunday',
            '0 0 1 * *' => 'Monthly on the 1st',
        ];

        return $map[$expression] ?? $expression;
    }
}

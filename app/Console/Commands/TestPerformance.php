<?php

namespace App\Console\Commands;

use App\Models\DubSyncProject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestPerformance extends Command
{
    protected $signature = 'test:performance';

    protected $description = 'Test performance of key database queries';

    public function handle()
    {
        $this->info('Testing Database Query Performance...');
        $this->line('');

        // Test 1: Projects index query (with selective columns)
        $this->info('Test 1: ProjectController::index() - Selective Columns');
        $this->testQuery(function () {
            return DubSyncProject::select([
                'id',
                'video_id',
                'youtube_url',
                'status',
                'created_at',
                'updated_at'
            ])->latest()->paginate(15);
        });

        $this->line('');

        // Test 2: DubSync index query
        $this->info('Test 2: DubSyncController::index() - With Segments');
        $this->testQuery(function () {
            return DubSyncProject::select([
                'id',
                'video_id',
                'youtube_url',
                'status',
                'segments',
                'created_at'
            ])->orderBy('created_at', 'desc')->paginate(10);
        });

        $this->line('');

        // Test 3: Full data query (BAD - for comparison)
        $this->info('Test 3: Full Data Query - ALL COLUMNS (Performance Comparison)');
        $this->testQuery(function () {
            return DubSyncProject::latest()->paginate(15);
        });

        $this->line('');
        $this->info('Performance test completed!');
    }

    private function testQuery($callback)
    {
        DB::enableQueryLog();

        $startTime = microtime(true);
        $result = $callback();
        $endTime = microtime(true);

        $queries = DB::getQueryLog();
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $this->line("  ✓ Execution Time: " . sprintf("%.2f", $executionTime) . "ms");
        $this->line("  ✓ Queries Executed: " . count($queries));

        if (!empty($queries)) {
            foreach ($queries as $query) {
                $this->line("    - " . $query['query']);
                if (!empty($query['bindings'])) {
                    $this->line("      Bindings: " . json_encode($query['bindings']));
                }
                $this->line("      Time: " . $query['time'] . "ms");
            }
        }

        if ($result instanceof \Illuminate\Pagination\Paginator) {
            $this->line("  ✓ Result Count: " . $result->count());
        } elseif ($result instanceof \Illuminate\Support\Collection) {
            $this->line("  ✓ Result Count: " . $result->count());
        }

        DB::disableQueryLog();
    }
}

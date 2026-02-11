@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Automation Reports</h1>
            <a href="{{ route('automation-reports.index') }}"
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition">
                Refresh
            </a>
        </div>

        {{-- Summary Statistics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-5">
                <div class="text-gray-500 text-xs font-semibold uppercase mb-1">Total Runs</div>
                <div class="text-2xl font-bold text-blue-600">{{ number_format($totalRuns) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <div class="text-gray-500 text-xs font-semibold uppercase mb-1">Successful</div>
                <div class="text-2xl font-bold text-green-600">{{ number_format($successfulRuns) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <div class="text-gray-500 text-xs font-semibold uppercase mb-1">Failed</div>
                <div class="text-2xl font-bold text-red-500">{{ number_format($failedRuns) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <div class="text-gray-500 text-xs font-semibold uppercase mb-1">Running</div>
                <div class="text-2xl font-bold text-yellow-500">{{ number_format($runningRuns) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <div class="text-gray-500 text-xs font-semibold uppercase mb-1">Last Run</div>
                <div class="text-sm font-bold text-gray-700">
                    {{ $lastRun ? $lastRun->started_at->diffForHumans() : 'Never' }}
                </div>
                @if($avgDuration)
                    <div class="text-xs text-gray-400 mt-1">Avg: {{ number_format($avgDuration, 1) }}s</div>
                @endif
            </div>
        </div>

        {{-- Upcoming Scheduled Jobs --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Upcoming Scheduled Jobs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Command</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Schedule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Next Run</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Options</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($upcomingJobs as $job)
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $job['command'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="font-medium">{{ $job['readable'] }}</span>
                                <span class="text-gray-400 text-xs ml-1">({{ $job['expression'] }})</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $job['next_run']->format('d/m/Y H:i') }}
                                <span class="text-gray-500 text-xs">({{ $job['next_run']->diffForHumans() }})</span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-1">
                                @if($job['without_overlapping'])
                                    <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">No Overlap</span>
                                @endif
                                @if($job['run_in_background'])
                                    <span class="px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full">Background</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">No scheduled jobs configured</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('automation-reports.index') }}"
                  class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Command</label>
                    <select name="command_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">All</option>
                        @foreach($commandNames as $name)
                            <option value="{{ $name }}" {{ request('command_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">All</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="running" {{ request('status') == 'running' ? 'selected' : '' }}>Running</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trigger</label>
                    <select name="trigger" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">All</option>
                        <option value="schedule" {{ request('trigger') == 'schedule' ? 'selected' : '' }}>Schedule</option>
                        <option value="manual" {{ request('trigger') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">Filter</button>
                    <a href="{{ route('automation-reports.index') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm transition">Clear</a>
                </div>
            </form>
        </div>

        {{-- Execution History Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Execution History</h2>
                <span class="text-sm text-gray-500">{{ $logs->total() }} records</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Command</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trigger</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->started_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">
                                {{ $log->command_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'success')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Success</span>
                                @elseif($log->status === 'failed')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Failed</span>
                                @elseif($log->status === 'running')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Running</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($log->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($log->duration_seconds)
                                    @if($log->duration_seconds >= 60)
                                        {{ floor($log->duration_seconds / 60) }}m {{ (int)($log->duration_seconds % 60) }}s
                                    @else
                                        {{ number_format($log->duration_seconds, 1) }}s
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $log->trigger === 'schedule' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($log->trigger) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                @if($log->output)
                                    <div class="text-xs text-gray-700 truncate" title="{{ $log->output }}">{{ Str::limit($log->output, 80) }}</div>
                                @endif
                                @if($log->meta_data)
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($log->meta_data as $key => $value)
                                            @if(!is_array($value))
                                                <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">{{ $key }}: {{ $value }}</span>
                                            @else
                                                <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">{{ $key }}: {{ count($value) }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                @if($log->error_message)
                                    <div class="text-xs text-red-600 mt-1 truncate max-w-xs" title="{{ $log->error_message }}">
                                        {{ Str::limit($log->error_message, 100) }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-lg mb-2">No automation logs recorded yet</div>
                                <div class="text-sm">Logs will appear here when scheduled jobs run</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <a href="{{ route('api-usage.index') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    ← Quay lại danh sách
                </a>
                <h1 class="text-3xl font-bold text-gray-800">📈 Thống kê API Usage</h1>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('api-usage.statistics') }}" class="flex gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                        class="border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    🔍 Lọc
                </button>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm mb-1">Tổng chi phí</p>
                        <p class="text-3xl font-bold">${{ number_format($totalCost, 4) }}</p>
                    </div>
                    <div class="text-5xl opacity-50">💰</div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm mb-1">Tổng số lần gọi</p>
                        <p class="text-3xl font-bold">{{ number_format($totalCalls) }}</p>
                    </div>
                    <div class="text-5xl opacity-50">📞</div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm mb-1">Tỷ lệ thành công</p>
                        <p class="text-3xl font-bold">{{ number_format($successRate, 1) }}%</p>
                    </div>
                    <div class="text-5xl opacity-50">✅</div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1: Daily Costs -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">📊 Xu hướng chi phí theo ngày</h2>
            <canvas id="dailyCostChart" height="80"></canvas>
        </div>

        <!-- Charts Row 2: Cost by Type & Purpose -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">🎯 Chi phí theo loại API</h2>
                <canvas id="costByTypeChart"></canvas>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">📝 Chi phí theo mục đích</h2>
                <canvas id="costByPurposeChart"></canvas>
            </div>
        </div>

        <!-- Top Expensive Projects -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">💎 Top 10 Projects chi phí cao nhất</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lần gọi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng chi phí</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topProjects as $project)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $project->project_id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $project->project->youtube_title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($project->total_calls) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                    ${{ number_format($project->total_cost, 4) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('projects.edit', $project->project_id) }}"
                                        class="text-blue-600 hover:text-blue-800">
                                        👁️ Xem project
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Không có dữ liệu
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cost by API Type Table -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">📊 Chi tiết theo loại API</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại API</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lần gọi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tổng chi phí</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chi phí TB/lần</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($costByApiType as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                {{ $item->api_type == 'OpenAI' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $item->api_type == 'ElevenLabs' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $item->api_type == 'YouTube' ? 'bg-red-100 text-red-800' : '' }}
                                {{ !in_array($item->api_type, ['OpenAI', 'ElevenLabs', 'YouTube']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $item->api_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->total_calls) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                    ${{ number_format($item->total_cost, 4) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($item->total_cost / $item->total_calls, 6) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Daily Cost Chart
        const dailyCostCtx = document.getElementById('dailyCostChart').getContext('2d');
        new Chart(dailyCostCtx, {
            type: 'line',
            data: {
                labels: @json($dailyCosts->pluck('date')),
                datasets: [{
                    label: 'Chi phí ($)',
                    data: @json($dailyCosts->pluck('total_cost')),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Chi phí: $' + context.parsed.y.toFixed(4);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Cost by API Type Chart
        const costByTypeCtx = document.getElementById('costByTypeChart').getContext('2d');
        new Chart(costByTypeCtx, {
            type: 'doughnut',
            data: {
                labels: @json($costByApiType->pluck('api_type')),
                datasets: [{
                    data: @json($costByApiType->pluck('total_cost')),
                    backgroundColor: [
                        'rgba(147, 51, 234, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': $' + context.parsed.toFixed(4);
                            }
                        }
                    }
                }
            }
        });

        // Cost by Purpose Chart
        const costByPurposeCtx = document.getElementById('costByPurposeChart').getContext('2d');
        new Chart(costByPurposeCtx, {
            type: 'bar',
            data: {
                labels: @json($costByPurpose->pluck('purpose')),
                datasets: [{
                    label: 'Chi phí ($)',
                    data: @json($costByPurpose->pluck('total_cost')),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Chi phí: $' + context.parsed.y.toFixed(4);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection

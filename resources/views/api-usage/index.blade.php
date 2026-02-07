@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">📊 API Usage Tracking</h1>
            <a href="{{ route('api-usage.statistics') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                📈 Xem thống kê
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Statistics Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500 text-sm font-semibold mb-1">Tổng chi phí</div>
                <div class="text-2xl font-bold text-blue-600">${{ number_format($totalCost, 4) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500 text-sm font-semibold mb-1">Tổng số lần gọi</div>
                <div class="text-2xl font-bold text-green-600">{{ number_format($totalCalls) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500 text-sm font-semibold mb-1">Thành công</div>
                <div class="text-2xl font-bold text-green-500">{{ number_format($successfulCalls) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500 text-sm font-semibold mb-1">Thất bại</div>
                <div class="text-2xl font-bold text-red-500">{{ number_format($failedCalls) }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('api-usage.index') }}"
                class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại API</label>
                    <select name="api_type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Tất cả</option>
                        @foreach ($apiTypes as $type)
                            <option value="{{ $type }}" {{ request('api_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mục đích</label>
                    <select name="purpose" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Tất cả</option>
                        @foreach ($purposes as $purpose)
                            <option value="{{ $purpose }}" {{ request('purpose') == $purpose ? 'selected' : '' }}>
                                {{ $purpose }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Tất cả</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Thành công</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        🔍 Lọc
                    </button>
                    <a href="{{ route('api-usage.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                        ✖️ Xóa
                    </a>
                </div>
            </form>
        </div>

        <!-- API Usage Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thời gian
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loại API
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mục đích
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Chi phí
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Metrics
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hành động
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($apiUsages as $usage)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $usage->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $usage->api_type == 'OpenAI' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $usage->api_type == 'ElevenLabs' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $usage->api_type == 'YouTube' ? 'bg-red-100 text-red-800' : '' }}
                                {{ !in_array($usage->api_type, ['OpenAI', 'ElevenLabs', 'YouTube']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $usage->api_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $usage->purpose }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ${{ number_format($usage->estimated_cost, 6) }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500">
                                    @if ($usage->tokens_used)
                                        <div>🔢 {{ number_format($usage->tokens_used) }} tokens</div>
                                    @endif
                                    @if ($usage->characters_used)
                                        <div>📝 {{ number_format($usage->characters_used) }} chars</div>
                                    @endif
                                    @if ($usage->duration_seconds)
                                        <div>⏱️ {{ number_format($usage->duration_seconds, 2) }}s</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $usage->status == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $usage->status == 'success' ? '✅ Thành công' : '❌ Thất bại' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if ($usage->project)
                                        <a href="{{ route('projects.edit', $usage->project_id) }}"
                                            class="text-blue-600 hover:text-blue-800">
                                            #{{ $usage->project_id }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('api-usage.show', $usage->id) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            👁️ Xem
                                        </a>
                                        <form action="{{ route('api-usage.destroy', $usage->id) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                🗑️ Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    Không có dữ liệu API usage
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $apiUsages->links() }}
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('api-usage.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Quay lại danh sách
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Chi tiết API Usage #{{ $apiUsage->id }}</h1>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Header Info -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $apiUsage->api_type }} - {{ $apiUsage->purpose }}</h2>
                        <p class="text-blue-100 text-sm mt-1">{{ $apiUsage->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">${{ number_format($apiUsage->estimated_cost, 6) }}</div>
                        <div class="text-blue-100 text-sm">Chi phí ước tính</div>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Thông tin cơ bản</h3>

                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Loại API</label>
                                <div class="mt-1">
                                    <span
                                        class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                    {{ $apiUsage->api_type == 'OpenAI' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $apiUsage->api_type == 'ElevenLabs' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $apiUsage->api_type == 'YouTube' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ !in_array($apiUsage->api_type, ['OpenAI', 'ElevenLabs', 'YouTube']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $apiUsage->api_type }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-500">Endpoint</label>
                                <div class="mt-1 text-gray-900 font-mono text-sm">
                                    {{ $apiUsage->api_endpoint ?? 'N/A' }}
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-500">Mục đích</label>
                                <div class="mt-1 text-gray-900">{{ $apiUsage->purpose }}</div>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-500">Trạng thái</label>
                                <div class="mt-1">
                                    <span
                                        class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                    {{ $apiUsage->status == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $apiUsage->status == 'success' ? '✅ Thành công' : '❌ Thất bại' }}
                                    </span>
                                </div>
                            </div>

                            @if ($apiUsage->description)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Mô tả</label>
                                    <div class="mt-1 text-gray-900">{{ $apiUsage->description }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Metrics & Relations -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Metrics & Liên kết</h3>

                        <div class="space-y-3">
                            @if ($apiUsage->tokens_used)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Tokens sử dụng</label>
                                    <div class="mt-1 text-gray-900 text-lg font-semibold">
                                        🔢 {{ number_format($apiUsage->tokens_used) }} tokens
                                    </div>
                                </div>
                            @endif

                            @if ($apiUsage->characters_used)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Ký tự sử dụng</label>
                                    <div class="mt-1 text-gray-900 text-lg font-semibold">
                                        📝 {{ number_format($apiUsage->characters_used) }} characters
                                    </div>
                                </div>
                            @endif

                            @if ($apiUsage->duration_seconds)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Thời lượng</label>
                                    <div class="mt-1 text-gray-900 text-lg font-semibold">
                                        ⏱️ {{ number_format($apiUsage->duration_seconds, 2) }} seconds
                                    </div>
                                </div>
                            @endif

                            @if ($apiUsage->project)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Project</label>
                                    <div class="mt-1">
                                        <a href="{{ route('projects.edit', $apiUsage->project_id) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                            📁 Project #{{ $apiUsage->project_id }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if ($apiUsage->user)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Người dùng</label>
                                    <div class="mt-1 text-gray-900">
                                        👤 {{ $apiUsage->user->name }} ({{ $apiUsage->user->email }})
                                    </div>
                                </div>
                            @endif

                            @if ($apiUsage->ip_address)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">IP Address</label>
                                    <div class="mt-1 text-gray-900 font-mono text-sm">{{ $apiUsage->ip_address }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                @if ($apiUsage->status == 'failed' && $apiUsage->error_message)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3 text-red-600">⚠️ Thông báo lỗi</h3>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <pre class="text-sm text-red-800 whitespace-pre-wrap">{{ $apiUsage->error_message }}</pre>
                        </div>
                    </div>
                @endif

                <!-- Request Data -->
                @if ($apiUsage->request_data)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">📤 Request Data</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-x-auto">
                            <pre class="text-sm text-gray-800">{{ json_encode($apiUsage->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif

                <!-- Response Data -->
                @if ($apiUsage->response_data)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">📥 Response Data</h3>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-x-auto">
                            <pre class="text-sm text-gray-800">{{ json_encode($apiUsage->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="mt-6 pt-6 border-t border-gray-200 flex justify-between">
                    <a href="{{ route('api-usage.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                        ← Quay lại
                    </a>

                    <form action="{{ route('api-usage.destroy', $apiUsage->id) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn xóa bản ghi này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                            🗑️ Xóa bản ghi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

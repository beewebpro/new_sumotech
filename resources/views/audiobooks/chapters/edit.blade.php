@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">Sửa Chương: {{ $chapter->title }}</h2>
                <a href="{{ route('audiobooks.show', $audioBook) }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Quay lại
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('audiobooks.chapters.update', [$audioBook, $chapter]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Book Info -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-gray-600">📚 <strong>{{ $audioBook->title }}</strong></p>
                        </div>

                        <!-- Chapter Number -->
                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Số thứ tự chương</label>
                                <input type="number" name="chapter_number" disabled
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100"
                                    value="{{ $chapter->chapter_number }}">
                                <p class="text-xs text-gray-500 mt-1">Không thể thay đổi số chương</p>
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề chương</label>
                                <input type="text" name="title"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('title') border-red-500 @enderror"
                                    value="{{ $chapter->title }}" required>
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Cover Image -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh minh họa chương</label>
                            @if ($chapter->cover_image)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $chapter->cover_image) }}" alt="Current cover"
                                        class="h-32 rounded">
                                </div>
                            @endif
                            <input type="file" name="cover_image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('cover_image') border-red-500 @enderror">
                            @error('cover_image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">📝 Nội dung chương</label>
                            <div class="mb-2 text-xs text-gray-600">
                                ⚠️ Nếu bạn thay đổi nội dung, tất cả các đoạn TTS cũ sẽ bị xóa và phải tạo lại
                            </div>
                            <textarea id="contentInput" name="content" rows="15"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono @error('content') border-red-500 @enderror"
                                required>{{ $chapter->content }}</textarea>
                            <p class="text-xs text-gray-500 mt-2">
                                📊 <span id="charCount">0</span> ký tự
                            </p>
                            @error('content')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- TTS Settings -->
                        <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-4">🎙️ Cài đặt TTS</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Voice Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Giọng đọc</label>
                                    <select name="tts_voice"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('tts_voice') border-red-500 @enderror"
                                        required>
                                        <option value="vi-VN-HoaiMyNeural"
                                            {{ $chapter->tts_voice == 'vi-VN-HoaiMyNeural' ? 'selected' : '' }}>
                                            🇻🇳 Hoài My (Nữ - Miền Bắc)
                                        </option>
                                        <option value="vi-VN-NamMinhNeural"
                                            {{ $chapter->tts_voice == 'vi-VN-NamMinhNeural' ? 'selected' : '' }}>
                                            🇻🇳 Nam Minh (Nam - Miền Nam)
                                        </option>
                                        <option value="en-US-AriaNeural"
                                            {{ $chapter->tts_voice == 'en-US-AriaNeural' ? 'selected' : '' }}>
                                            🇺🇸 Aria (Female - US)
                                        </option>
                                        <option value="en-US-GuyNeural"
                                            {{ $chapter->tts_voice == 'en-US-GuyNeural' ? 'selected' : '' }}>
                                            🇺🇸 Guy (Male - US)
                                        </option>
                                        <option value="en-GB-SoniaNeural"
                                            {{ $chapter->tts_voice == 'en-GB-SoniaNeural' ? 'selected' : '' }}>
                                            🇬🇧 Sonia (Female - UK)
                                        </option>
                                    </select>
                                    @error('tts_voice')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Speed -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tốc độ đọc</label>
                                    <select name="tts_speed"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('tts_speed') border-red-500 @enderror"
                                        required>
                                        <option value="0.5" {{ $chapter->tts_speed == 0.5 ? 'selected' : '' }}>0.5x (Rất
                                            chậm)</option>
                                        <option value="0.75" {{ $chapter->tts_speed == 0.75 ? 'selected' : '' }}>0.75x
                                            (Chậm)</option>
                                        <option value="1.0" {{ $chapter->tts_speed == 1.0 ? 'selected' : '' }}>1.0x
                                            (Bình thường)</option>
                                        <option value="1.5" {{ $chapter->tts_speed == 1.5 ? 'selected' : '' }}>1.5x
                                            (Nhanh)</option>
                                        <option value="2.0" {{ $chapter->tts_speed == 2.0 ? 'selected' : '' }}>2.0x (Rất
                                            nhanh)</option>
                                    </select>
                                    @error('tts_speed')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current Chunks Info -->
                        @if ($chapter->chunks->count() > 0)
                            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-gray-600">
                                    📦 Chương hiện có <strong>{{ $chapter->total_chunks }}</strong> đoạn TTS
                                    @if ($chapter->chunks->where('status', 'completed')->count() > 0)
                                        | ✅ {{ $chapter->chunks->where('status', 'completed')->count() }} hoàn tất
                                    @endif
                                </p>
                            </div>
                        @endif

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                ✓ Cập nhật Chương
                            </button>
                            <a href="{{ route('audiobooks.show', $audioBook) }}"
                                class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Character counter
        const contentInput = document.getElementById('contentInput');
        const charCount = document.getElementById('charCount');

        contentInput.addEventListener('input', function() {
            charCount.textContent = this.value.length.toLocaleString('vi-VN');
        });

        // Trigger on load
        charCount.textContent = contentInput.value.length.toLocaleString('vi-VN');
    </script>
@endsection

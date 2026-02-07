@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">📖 Thêm Chương Mới</h2>
                <a href="{{ route('audiobooks.show', $audioBook) }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Quay lại
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('audiobooks.chapters.store', $audioBook) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Book Info -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-gray-600">📚 <strong>{{ $audioBook->title }}</strong></p>
                        </div>

                        <!-- Chapter Number -->
                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Số thứ tự chương</label>
                                <input type="number" name="chapter_number"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('chapter_number') border-red-500 @enderror"
                                    value="{{ $nextChapter }}" min="1" required>
                                @error('chapter_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề chương</label>
                                <input type="text" name="title"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('title') border-red-500 @enderror"
                                    placeholder="VD: Giới thiệu nhân vật" value="{{ old('title') }}" required>
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Cover Image -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh minh họa chương (không bắt
                                buộc)</label>
                            <input type="file" name="cover_image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('cover_image') border-red-500 @enderror">
                            <p class="text-xs text-gray-500 mt-1">Để trống nếu không có</p>
                            @error('cover_image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content - Main Text Area -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">📝 Nội dung chương</label>
                            <div class="mb-2 text-xs text-gray-600">
                                ⚠️ Nội dung sẽ tự động chia thành các đoạn nhỏ (max 1000 ký tự) để xử lý TTS
                            </div>
                            <textarea id="contentInput" name="content" rows="15"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono @error('content') border-red-500 @enderror"
                                placeholder="Nhập nội dung chương ở đây..." required>{{ old('content') }}</textarea>
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
                                        <option value="vi-VN-HoaiMyNeural" selected>🇻🇳 Hoài My (Nữ - Miền Bắc)</option>
                                        <option value="vi-VN-NamMinhNeural">🇻🇳 Nam Minh (Nam - Miền Nam)</option>
                                        <option value="en-US-AriaNeural">🇺🇸 Aria (Female - US)</option>
                                        <option value="en-US-GuyNeural">🇺🇸 Guy (Male - US)</option>
                                        <option value="en-GB-SoniaNeural">🇬🇧 Sonia (Female - UK)</option>
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
                                        <option value="0.5">0.5x (Rất chậm)</option>
                                        <option value="0.75">0.75x (Chậm)</option>
                                        <option value="1.0" selected>1.0x (Bình thường)</option>
                                        <option value="1.5">1.5x (Nhanh)</option>
                                        <option value="2.0">2.0x (Rất nhanh)</option>
                                    </select>
                                    @error('tts_speed')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Preview Info -->
                        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-sm text-gray-600">
                                💡 <strong>Thông tin:</strong> Khi tạo chương, hệ thống sẽ tự động chia nội dung thành các
                                đoạn nhỏ.
                                Sau đó bạn có thể nhấn nút "🎙️ Tạo TTS" để tạo âm thanh cho chương này.
                            </p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                ✓ Tạo Chương
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

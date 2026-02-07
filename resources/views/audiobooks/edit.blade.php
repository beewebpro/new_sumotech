@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">Sửa Sách: {{ $audioBook->title }}</h2>
                <a href="{{ route('audiobooks.show', $audioBook) }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Quay lại
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('audiobooks.update', $audioBook) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Channel Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Channel 📺</label>
                            <select name="youtube_channel_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('youtube_channel_id') border-red-500 @enderror"
                                required>
                                <option value="">-- Chọn Channel --</option>
                                @foreach ($youtubeChannels as $channel)
                                    <option value="{{ $channel->id }}"
                                        {{ $audioBook->youtube_channel_id == $channel->id ? 'selected' : '' }}>
                                        {{ $channel->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('youtube_channel_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề sách</label>
                            <input type="text" name="title"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('title') border-red-500 @enderror"
                                value="{{ $audioBook->title }}" required>
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Book Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phân loại</label>
                            @php
                                $bookType = $audioBook->book_type ?? 'sach';
                                $knownTypes = ['sach', 'truyen', 'tieu_thuyet', 'truyen_ngan'];
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <select name="book_type" id="bookTypeSelect"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('book_type') border-red-500 @enderror">
                                    <option value="sach" {{ $bookType == 'sach' ? 'selected' : '' }}>Sách</option>
                                    <option value="truyen" {{ $bookType == 'truyen' ? 'selected' : '' }}>Truyện</option>
                                    <option value="tieu_thuyet" {{ $bookType == 'tieu_thuyet' ? 'selected' : '' }}>Tiểu
                                        thuyết</option>
                                    <option value="truyen_ngan" {{ $bookType == 'truyen_ngan' ? 'selected' : '' }}>Truyện
                                        ngắn</option>
                                    <option value="khac" {{ !in_array($bookType, $knownTypes) ? 'selected' : '' }}>Khác
                                        (tự nhập)</option>
                                </select>
                                <input type="text" name="book_type_custom" id="bookTypeCustom"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="Nhập phân loại..."
                                    value="{{ !in_array($bookType, $knownTypes) ? $bookType : '' }}">
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Nếu chọn "Khác", hãy nhập phân loại ở ô bên cạnh.</p>
                            @error('book_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                            <textarea name="description" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('description') border-red-500 @enderror">{{ $audioBook->description }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cover Image -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh bìa sách</label>
                            @if ($audioBook->cover_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $audioBook->cover_image) }}" alt="Current cover"
                                        class="h-32 rounded">
                                </div>
                            @endif
                            <input type="file" name="cover_image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('cover_image') border-red-500 @enderror">
                            @error('cover_image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Language -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ngôn ngữ</label>
                            <select name="language"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm @error('language') border-red-500 @enderror"
                                required>
                                <option value="vi" {{ $audioBook->language == 'vi' ? 'selected' : '' }}>Tiếng Việt
                                </option>
                                <option value="en" {{ $audioBook->language == 'en' ? 'selected' : '' }}>English
                                </option>
                                <option value="es" {{ $audioBook->language == 'es' ? 'selected' : '' }}>Español
                                </option>
                                <option value="fr" {{ $audioBook->language == 'fr' ? 'selected' : '' }}>Français
                                </option>
                                <option value="de" {{ $audioBook->language == 'de' ? 'selected' : '' }}>Deutsch
                                </option>
                                <option value="ja" {{ $audioBook->language == 'ja' ? 'selected' : '' }}>日本語</option>
                                <option value="ko" {{ $audioBook->language == 'ko' ? 'selected' : '' }}>한국어</option>
                            </select>
                            @error('language')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                ✓ Cập nhật
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
@endsection

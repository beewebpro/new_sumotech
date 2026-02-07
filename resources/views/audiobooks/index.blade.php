@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">📚 Audiobooks</h2>
                <a href="{{ route('audiobooks.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    + Tạo Sách
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($audioBooks->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($audioBooks as $book)
                                <div
                                    class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-200">
                                    @if ($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                            class="w-full h-48 object-cover">
                                    @else
                                        <div
                                            class="w-full h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                            <span class="text-4xl">📚</span>
                                        </div>
                                    @endif

                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $book->title }}</h3>
                                        <p class="text-sm text-gray-600 mb-3">
                                            📺 {{ $book->youtubeChannel?->title ?? 'Unknown Channel' }}
                                        </p>
                                        <p class="text-sm text-gray-600 mb-4">
                                            📖 {{ $book->total_chapters }} chương
                                        </p>

                                        <div class="flex gap-2">
                                            <a href="{{ route('audiobooks.show', $book) }}"
                                                class="flex-1 text-center bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold py-2 px-3 rounded transition duration-200">
                                                Xem
                                            </a>
                                            <a href="{{ route('audiobooks.edit', $book) }}"
                                                class="flex-1 text-center bg-yellow-100 hover:bg-yellow-200 text-yellow-700 font-semibold py-2 px-3 rounded transition duration-200">
                                                Sửa
                                            </a>
                                            <form action="{{ route('audiobooks.destroy', $book) }}" method="POST"
                                                class="flex-1" onsubmit="return confirm('Xóa sách này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full text-center bg-red-100 hover:bg-red-200 text-red-700 font-semibold py-2 px-3 rounded transition duration-200">
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $audioBooks->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg mb-4">Chưa có audiobooks nào</p>
                            <a href="{{ route('audiobooks.create') }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                                + Tạo Sách Đầu Tiên
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

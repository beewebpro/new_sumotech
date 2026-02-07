<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Video Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('youtube-channels.contents.edit', [$youtubeChannel, $content->id]) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Edit
                </a>
                <a href="{{ route('youtube-channels.contents.index', $youtubeChannel) }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Back to list
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-shrink-0">
                            @if ($content->thumbnail_url)
                                <img src="{{ $content->thumbnail_url }}" alt="Thumbnail"
                                    class="w-48 h-32 object-cover rounded-lg border">
                            @else
                                <div
                                    class="w-48 h-32 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                    No Thumbnail
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $content->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">Video ID: {{ $content->video_id }}</p>
                            <p class="text-sm text-gray-600 mt-1">Published At:
                                {{ $content->published_at?->format('d/m/Y') ?? '—' }}</p>
                            <p class="text-sm text-gray-600 mt-1">Duration:
                                {{ $content->duration_seconds ? gmdate('H:i:s', $content->duration_seconds) : '—' }}</p>
                            <p class="text-sm text-gray-600 mt-1">Video URL:
                                @if ($content->video_url)
                                    <a href="{{ $content->video_url }}" target="_blank"
                                        class="text-red-600 hover:text-red-700">Open</a>
                                @else
                                    —
                                @endif
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500">Views</div>
                                    <div class="text-lg font-semibold text-gray-900">
                                        {{ number_format($content->views_count ?? 0) }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500">Likes</div>
                                    <div class="text-lg font-semibold text-gray-900">
                                        {{ number_format($content->likes_count ?? 0) }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-500">Comments</div>
                                    <div class="text-lg font-semibold text-gray-900">
                                        {{ number_format($content->comments_count ?? 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Description</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-line">
                            {{ $content->description ?? 'No description available.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

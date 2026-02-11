@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">
                    {{ __('YouTube Channels') }}
                </h2>
                <a href="{{ route('youtube-channels.create') }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    + New Channel
                </a>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if ($message = Session::get('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <span class="block sm:inline">{{ $message }}</span>
                        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3"
                            onclick="this.parentElement.style.display='none';">
                            <span class="text-2xl leading-none">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if ($channels->count() > 0)
                            <div class="mb-4 flex items-center justify-end">
                                <div class="flex items-center gap-2">
                                    <label for="perPageSelect" class="text-sm text-gray-600">Items per page:</label>
                                    <select id="perPageSelect"
                                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <option value="10" @if (request('per_page') == 10) selected @endif>10</option>
                                        <option value="20" @if (request('per_page') == 20 || request('per_page') === null) selected @endif>20</option>
                                        <option value="25" @if (request('per_page') == 25) selected @endif>25</option>
                                        <option value="50" @if (request('per_page') == 50) selected @endif>50</option>
                                        <option value="100" @if (request('per_page') == 100) selected @endif>100
                                        </option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        @if ($channels->count() === 0)
                            <div class="text-center py-12">
                                <p class="text-gray-600">No channels yet.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Channel ID</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Content Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Title</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Custom URL</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Subscribers</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Videos</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Views</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($channels as $channel)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $channel->channel_id }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($channel->content_type === 'audiobook')
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                            </svg>
                                                            Audiobook
                                                        </span>
                                                    @elseif($channel->content_type === 'dub')
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                            </svg>
                                                            Dub
                                                        </span>
                                                    @elseif($channel->content_type === 'self_creative')
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                            </svg>
                                                            Creative
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                                            Not Set
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    <div class="flex items-center gap-3">
                                                        @if ($channel->thumbnail_url)
                                                            <img src="{{ str_starts_with($channel->thumbnail_url, 'http') ? $channel->thumbnail_url : asset('storage/' . $channel->thumbnail_url) }}"
                                                                alt="Thumbnail"
                                                                class="w-10 h-10 rounded object-cover border">
                                                        @else
                                                            <div
                                                                class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center text-gray-400">
                                                                <i class="ri-image-line"></i>
                                                            </div>
                                                        @endif
                                                        <span>{{ $channel->title }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    {{ $channel->custom_url ?? '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    {{ number_format($channel->subscribers_count ?? 0) }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    {{ number_format($channel->videos_count ?? 0) }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    {{ number_format($channel->views_count ?? 0) }}</td>
                                                <td class="px-4 py-3 text-right text-sm">
                                                    <div class="inline-flex gap-2">
                                                        <a href="{{ route('youtube-channels.show', $channel) }}"
                                                            class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">View</a>
                                                        <a href="{{ route('youtube-channels.edit', $channel) }}"
                                                            class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700">Edit</a>
                                                        <form action="{{ route('youtube-channels.destroy', $channel) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Delete this channel?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6">
                                {{ $channels->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function() {
                const perPageSelect = document.getElementById('perPageSelect');
                if (perPageSelect) {
                    perPageSelect.addEventListener('change', function() {
                        const perPage = this.value;
                        const url = new URL(window.location);
                        url.searchParams.set('per_page', perPage);
                        url.searchParams.set('page', 1); // Reset to page 1
                        window.location.href = url.toString();
                    });
                }
            })();
        </script>
    @endsection

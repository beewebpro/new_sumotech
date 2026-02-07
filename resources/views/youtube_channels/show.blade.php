@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">
                    {{ __('Channel Details') }}
                </h2>
                <div class="flex gap-2">
                    <a href="{{ route('youtube-channels.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Back
                    </a>
                    <a href="{{ route('youtube-channels.edit', $youtubeChannel) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Edit
                    </a>
                    <button id="newVideoBtn"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        + New Video
                    </button>
                </div>
            </div>
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                @if ($message = Session::get('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ $message }}
                        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3"
                            onclick="this.parentElement.style.display='none';">
                            <span class="text-2xl leading-none">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="flex-shrink-0">
                                @if ($youtubeChannel->thumbnail_url)
                                    @php
                                        $thumbnailUrl = \Illuminate\Support\Str::startsWith(
                                            $youtubeChannel->thumbnail_url,
                                            ['http://', 'https://'],
                                        )
                                            ? $youtubeChannel->thumbnail_url
                                            : asset('storage/' . ltrim($youtubeChannel->thumbnail_url, '/'));
                                    @endphp
                                    <img src="{{ $thumbnailUrl }}" alt="Thumbnail"
                                        class="w-48 h-32 rounded object-cover border">
                                @else
                                    <div
                                        class="w-48 h-32 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                        No Thumbnail
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $youtubeChannel->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">Channel ID: {{ $youtubeChannel->channel_id }}</p>
                                @if ($youtubeChannel->channel_url)
                                    <a href="{{ $youtubeChannel->channel_url }}" target="_blank"
                                        class="text-xs text-red-600 hover:text-red-700 break-words">Open channel</a>
                                @endif
                                <p class="text-sm text-gray-600 mt-1">Country: {{ $youtubeChannel->country ?? '—' }}</p>
                                <p class="text-sm text-gray-600 mt-1">Published At:
                                    {{ $youtubeChannel->published_at?->format('d/m/Y H:i') ?? '—' }}</p>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="text-xs text-gray-500">Subscribers</div>
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ number_format($youtubeChannel->subscribers_count ?? 0) }}
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="text-xs text-gray-500">Videos</div>
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ number_format($youtubeChannel->videos_count ?? 0) }}
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="text-xs text-gray-500">Views</div>
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ number_format($youtubeChannel->views_count ?? 0) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Description</h4>
                            <p class="text-sm text-gray-700 whitespace-pre-line">
                                {{ $youtubeChannel->description ?? 'No description available.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                    <div class="p-6 text-gray-900">
                        <div class="border-b border-gray-200 mb-4">
                            <nav class="-mb-px flex gap-6" aria-label="Tabs">
                                <button id="tabDubsyncBtn"
                                    class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    Lồng tiếng
                                </button>
                                <button id="tabAudiobooksBtn"
                                    class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    Audio books
                                </button>
                                <button id="tabSpeakersBtn"
                                    class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                    🎙️ MC / Speakers
                                </button>
                            </nav>
                        </div>

                        <div id="tabDubsync" class="tab-content">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">Lồng tiếng</h3>
                                <div class="flex items-center gap-2">
                                    <button id="bulkTranscriptBtn" type="button"
                                        class="bg-green-100 text-green-700 font-semibold py-2 px-4 rounded-lg transition duration-200 opacity-60 cursor-not-allowed"
                                        disabled>
                                        Get transcript
                                    </button>
                                    <button id="bulkDeleteBtn" type="button"
                                        class="bg-red-100 text-red-700 font-semibold py-2 px-4 rounded-lg transition duration-200 opacity-60 cursor-not-allowed"
                                        disabled>
                                        Delete selected
                                    </button>
                                    <form action="{{ route('youtube-channels.fetch.videos', $youtubeChannel) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                            Fetch video
                                        </button>
                                    </form>
                                    <button id="newVideoBtnInline"
                                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                        + New Video
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Section -->
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <form method="GET" action="{{ route('youtube-channels.show', $youtubeChannel) }}"
                                    class="flex gap-4 items-end flex-wrap">
                                    <div class="flex-1 min-w-64">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Search by name or video
                                            ID</label>
                                        <input type="text" name="search" placeholder="Enter video name or ID..."
                                            value="{{ $search }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                    </div>
                                    <div class="flex-1 min-w-48">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                                        <select name="status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                            <option value="">All Status</option>
                                            <option value="new" @if ($status === 'new') selected @endif>New
                                            </option>
                                            <option value="pending" @if ($status === 'pending') selected @endif>
                                                Pending</option>
                                            <option value="processing" @if ($status === 'processing') selected @endif>
                                                Processing</option>
                                            <option value="transcribed" @if ($status === 'transcribed') selected @endif>
                                                Transcribed</option>
                                            <option value="translated" @if ($status === 'translated') selected @endif>
                                                Translated</option>
                                            <option value="tts_generated" @if ($status === 'tts_generated') selected @endif>
                                                TTS Generated</option>
                                            <option value="aligned" @if ($status === 'aligned') selected @endif>
                                                Aligned</option>
                                            <option value="merged" @if ($status === 'merged') selected @endif>Merged
                                            </option>
                                            <option value="completed" @if ($status === 'completed') selected @endif>
                                                Completed</option>
                                            <option value="error" @if ($status === 'error') selected @endif>Error
                                            </option>
                                        </select>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                                            Filter
                                        </button>
                                        <a href="{{ route('youtube-channels.show', $youtubeChannel) }}"
                                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg text-sm font-medium">
                                            Clear
                                        </a>
                                    </div>
                                </form>
                            </div>

                            @if ($projects->count() === 0)
                                <div class="text-center py-8 text-gray-600">No videos yet.</div>
                            @else
                                <div class="mb-4 flex items-center justify-end">
                                    <div class="flex items-center gap-2">
                                        <label for="perPageSelect" class="text-sm text-gray-600">Items per page:</label>
                                        <select id="perPageSelect"
                                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                            <option value="10" @if (request('per_page') == 10 || request('per_page') === null) selected @endif>10
                                            </option>
                                            <option value="20" @if (request('per_page') == 20) selected @endif>20
                                            </option>
                                            <option value="50" @if (request('per_page') == 50) selected @endif>50
                                            </option>
                                            <option value="100" @if (request('per_page') == 100) selected @endif>100
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <form id="bulkDeleteForm"
                                    action="{{ route('youtube-channels.projects.bulk.destroy', $youtubeChannel) }}"
                                    method="POST" class="hidden">
                                    @csrf
                                    <div id="bulkDeleteInputs"></div>
                                </form>
                                <div class="overflow-x-hidden">
                                    <table class="w-full table-fixed divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">
                                                    <input id="selectAllProjects" type="checkbox"
                                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-2/5">
                                                    Video</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/5">
                                                    Duration</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/6">
                                                    Status</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/6">
                                                    Created</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-1/6">
                                                    Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($projects as $project)
                                                <tr class="cursor-pointer hover:bg-gray-50"
                                                    data-href="{{ route('projects.edit', $project) }}"
                                                    data-transcript-url="{{ route('projects.get.transcript.async', $project) }}"
                                                    data-status="{{ $project->status }}">
                                                    <td class="px-4 py-3 align-top">
                                                        <input type="checkbox" data-project-id="{{ $project->id }}"
                                                            class="project-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500" />
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-900 align-top">
                                                        <div class="flex items-start gap-3">
                                                            @if ($project->youtube_thumbnail)
                                                                <img src="{{ $project->youtube_thumbnail }}"
                                                                    alt="Thumbnail"
                                                                    class="w-12 h-8 rounded object-cover border flex-shrink-0">
                                                            @else
                                                                <div
                                                                    class="w-12 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                                    <i class="ri-image-line"></i>
                                                                </div>
                                                            @endif
                                                            <div class="min-w-0">
                                                                <div class="font-medium break-words">
                                                                    {{ $project->youtube_title_vi ?? ($project->youtube_title ?? $project->video_id) }}
                                                                </div>
                                                                @if ($project->youtube_url)
                                                                    <a href="{{ $project->youtube_url }}" target="_blank"
                                                                        class="text-xs text-red-600 hover:text-red-700 break-words">Open
                                                                        video</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 align-top">
                                                        {{ $project->youtube_duration ?? '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 align-top">
                                                        @php
                                                            $statusColors = [
                                                                'new' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                                'pending' =>
                                                                    'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                                'processing' =>
                                                                    'bg-purple-100 text-purple-800 border-purple-200',
                                                                'transcribed' =>
                                                                    'bg-cyan-100 text-cyan-800 border-cyan-200',
                                                                'translated' =>
                                                                    'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                                'tts_generated' =>
                                                                    'bg-pink-100 text-pink-800 border-pink-200',
                                                                'aligned' =>
                                                                    'bg-teal-100 text-teal-800 border-teal-200',
                                                                'merged' => 'bg-lime-100 text-lime-800 border-lime-200',
                                                                'completed' =>
                                                                    'bg-green-100 text-green-800 border-green-200',
                                                                'error' => 'bg-red-100 text-red-800 border-red-200',
                                                            ];
                                                            $statusClass =
                                                                $statusColors[$project->status] ??
                                                                'bg-gray-100 text-gray-800 border-gray-200';
                                                        @endphp
                                                        <span
                                                            class="project-status px-2 py-1 rounded text-xs font-semibold border {{ $statusClass }}">
                                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                        </span>
                                                        @if ($project->status === 'error' && $project->error_message)
                                                            <div class="mt-1 text-xs text-red-600"
                                                                title="{{ $project->error_message }}">
                                                                <i class="ri-error-warning-line"></i>
                                                                <span
                                                                    class="truncate max-w-[150px] inline-block align-bottom">{{ Str::limit($project->error_message, 50) }}</span>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600 align-top">
                                                        {{ $project->created_at?->format('d/m/Y H:i') ?? '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-right text-sm align-top">
                                                        <div class="inline-flex flex-wrap gap-2 justify-end">
                                                            @if ($project->status === 'error')
                                                                <button
                                                                    class="retry-transcript-btn px-3 py-1.5 bg-orange-600 text-white rounded hover:bg-orange-700"
                                                                    data-project-id="{{ $project->id }}"
                                                                    data-youtube-url="{{ $project->youtube_url }}"
                                                                    data-transcript-url="{{ route('projects.get.transcript.async', $project) }}">
                                                                    <i class="ri-refresh-line"></i> Retry
                                                                </button>
                                                            @endif
                                                            <a href="{{ route('projects.edit', $project) }}"
                                                                class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700">Edit</a>
                                                            <span
                                                                class="row-spinner hidden items-center gap-2 text-xs text-gray-500">
                                                                <svg class="animate-spin h-4 w-4 text-green-600"
                                                                    viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12"
                                                                        cy="12" r="10" stroke="currentColor"
                                                                        stroke-width="4" fill="none"></circle>
                                                                    <path class="opacity-75" fill="currentColor"
                                                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                                                </svg>
                                                                Processing...
                                                            </span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-6">
                                    {{ $projects->links() }}
                                </div>
                            @endif

                            <!-- Reference Channels Section -->
                            <div class="mt-8 pt-8 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold">Reference Channels</h3>
                                    <a href="{{ route('youtube-channels.edit', $youtubeChannel) }}"
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                                        Manage
                                    </a>
                                </div>

                                @if ($referenceChannels->count() === 0)
                                    <div class="text-center py-8 text-gray-600">No reference channels yet.</div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($referenceChannels as $ref)
                                            <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                                                <div class="flex-shrink-0">
                                                    @if ($ref->ref_thumbnail_url)
                                                        <img src="{{ $ref->ref_thumbnail_url }}" alt="Thumbnail"
                                                            class="w-14 h-14 rounded-full object-cover border">
                                                    @else
                                                        <div
                                                            class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                            <i class="ri-youtube-line"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-semibold text-gray-900 truncate">
                                                        {{ $ref->ref_title ?? 'Untitled Channel' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $ref->ref_channel_id ?? '—' }}
                                                    </div>
                                                    <div class="text-xs text-gray-400 truncate">
                                                        {{ $ref->ref_channel_url }}
                                                    </div>
                                                    @if ($ref->ref_description)
                                                        <div class="text-xs text-gray-600 mt-1 line-clamp-2">
                                                            {{ $ref->ref_description }}
                                                        </div>
                                                    @endif
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Fetch every {{ $ref->fetch_interval_days ?? 7 }} day(s)
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a href="{{ $ref->ref_channel_url }}" target="_blank"
                                                        class="text-xs text-red-600 hover:text-red-700">Open</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div id="tabAudiobooks" class="tab-content hidden">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">Audio books</h3>
                                <a href="{{ route('audiobooks.create') }}?youtube_channel_id={{ $youtubeChannel->id }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    + New Audio book
                                </a>
                            </div>

                            @if ($audioBooks->count() === 0)
                                <div class="text-center py-8 text-gray-600">No audio books yet.</div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($audioBooks as $audioBook)
                                        <div
                                            class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-200 flex flex-col">
                                            <div class="relative bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center p-4"
                                                style="min-height: 200px;">
                                                @if ($audioBook->cover_image)
                                                    <img src="{{ asset('storage/' . $audioBook->cover_image) }}"
                                                        alt="Cover"
                                                        class="max-h-48 w-auto object-contain rounded shadow-md">
                                                @else
                                                    <div
                                                        class="w-32 h-44 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                                        <i class="ri-book-line text-4xl"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="p-4 flex-1 flex flex-col">
                                                <h4 class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ $audioBook->title }}
                                                </h4>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $audioBook->total_chapters ?? 0 }} chapters
                                                </p>
                                                @if ($audioBook->description)
                                                    <p class="text-xs text-gray-600 mt-2 line-clamp-2 flex-1">
                                                        {{ $audioBook->description }}
                                                    </p>
                                                @endif
                                                <div class="flex gap-2 mt-4">
                                                    <a href="{{ route('audiobooks.show', $audioBook) }}"
                                                        class="flex-1 text-center px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700">
                                                        View
                                                    </a>
                                                    <a href="{{ route('audiobooks.edit', $audioBook) }}"
                                                        class="flex-1 text-center px-3 py-1.5 bg-gray-200 text-gray-800 rounded text-xs font-medium hover:bg-gray-300">
                                                        Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-6">
                                    {{ $audioBooks->links() }}
                                </div>
                            @endif
                        </div>

                        <!-- MC / Speakers Tab -->
                        <div id="tabSpeakers" class="tab-content hidden">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">🎙️ MC / Speakers</h3>
                                <button type="button" onclick="openSpeakerModal()"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    + Thêm MC
                                </button>
                            </div>

                            <p class="text-sm text-gray-600 mb-4">
                                MC là người thuyết minh cho kênh. Upload hình avatar hoặc hình khung cảnh để tạo speaker với
                                hiệu ứng lip-sync (nhép miệng).
                            </p>

                            <div id="speakersContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div id="speakersLoading" class="col-span-full text-center py-8 text-gray-400">
                                    <div
                                        class="animate-spin inline-block w-6 h-6 border-2 border-gray-300 border-t-purple-600 rounded-full mr-2">
                                    </div>
                                    Đang tải...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Speaker Modal -->
        <div id="speakerModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="speakerModalTitle" class="text-lg font-semibold">Thêm MC mới</h3>
                    <button type="button" onclick="closeSpeakerModal()"
                        class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                <form id="speakerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="speakerId" name="speaker_id" value="">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên MC <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="speakerName" name="name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    placeholder="VD: Minh Châu, MC Hương...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính <span
                                        class="text-red-500">*</span></label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="gender" value="female" checked
                                            class="text-purple-600">
                                        <span>👩 Nữ</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="gender" value="male" class="text-purple-600">
                                        <span>👨 Nam</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Avatar / Hình đại diện</label>
                                <input type="file" id="speakerAvatar" name="avatar" accept="image/*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <p class="text-xs text-gray-500 mt-1">Ảnh chân dung hoặc hình khung cảnh cho lip-sync</p>
                                <div id="speakerAvatarPreview" class="mt-2 hidden">
                                    <img src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg border">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                                <textarea id="speakerDescription" name="description" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    placeholder="Giọng nữ miền Nam ấm áp, phù hợp đọc truyện tình cảm..."></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">TTS Provider mặc định</label>
                                <select id="speakerVoiceProvider" name="default_voice_provider"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">-- Chọn TTS Provider --</option>
                                    <option value="openai">🤖 OpenAI TTS</option>
                                    <option value="gemini">✨ Gemini Pro TTS</option>
                                    <option value="microsoft">🪟 Microsoft TTS</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên giọng mặc định</label>
                                <select id="speakerVoiceName" name="default_voice_name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">-- Chọn TTS Provider trước --</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phong cách giọng nói</label>
                                <textarea id="speakerVoiceStyle" name="voice_style" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    placeholder="VD: Đọc với giọng ấm áp, chậm rãi, phong cách kể chuyện..."></textarea>
                            </div>

                            <!-- Lip Sync Settings -->
                            <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <label class="flex items-center gap-2 cursor-pointer mb-3">
                                    <input type="checkbox" id="speakerLipSyncEnabled" name="lip_sync_enabled"
                                        value="1" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                                    <span class="text-sm font-medium text-gray-700">🎬 Bật hiệu ứng nhép miệng
                                        (Lip-sync)</span>
                                </label>

                                <div id="lipSyncSettings" class="hidden space-y-2">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-gray-600 w-24">Độ nhạy:</label>
                                        <input type="range" id="lipSyncSensitivity"
                                            name="lip_sync_settings[sensitivity]" min="0" max="1"
                                            step="0.1" value="0.5" class="flex-1">
                                        <span id="lipSyncSensitivityValue" class="text-xs text-gray-500 w-8">0.5</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs text-gray-600 w-24">Kiểu:</label>
                                        <select name="lip_sync_settings[style]"
                                            class="flex-1 px-2 py-1 border border-gray-300 rounded text-xs">
                                            <option value="natural">Tự nhiên</option>
                                            <option value="exaggerated">Phóng đại</option>
                                            <option value="subtle">Tinh tế</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh bổ sung</label>
                                <input type="file" id="speakerAdditionalImages" name="additional_images[]"
                                    accept="image/*" multiple
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <p class="text-xs text-gray-500 mt-1">Các hình ảnh khác (pose khác, khung cảnh...)</p>
                            </div>
                        </div>
                    </div>

                    <div id="speakerFormStatus" class="mt-4 text-sm"></div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" id="speakerSubmitBtn"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            💾 Lưu MC
                        </button>
                        <button type="button" onclick="closeSpeakerModal()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="newVideoModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Create New Video</h3>
                    <button id="closeNewVideoModal" class="text-gray-500 hover:text-gray-700">
                        &times;
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-4">Choose the type to create:</p>
                <div class="space-y-3">
                    <a href="{{ route('projects.create') }}?youtube_channel_id={{ $youtubeChannel->id }}"
                        class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        1) Lồng tiếng
                    </a>
                    <a href="{{ route('youtube-channels.contents.create', $youtubeChannel) }}"
                        class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        2) Từ kịch bản của tôi
                    </a>
                    <a href="{{ route('audiobooks.create') }}?youtube_channel_id={{ $youtubeChannel->id }}"
                        class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        3) Audiobook
                    </a>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle per_page selection
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

                const modal = document.getElementById('newVideoModal');
                const openBtns = [document.getElementById('newVideoBtn'), document.getElementById('newVideoBtnInline')];
                const closeBtn = document.getElementById('closeNewVideoModal');
                const tabDubsyncBtn = document.getElementById('tabDubsyncBtn');
                const tabAudiobooksBtn = document.getElementById('tabAudiobooksBtn');
                const tabSpeakersBtn = document.getElementById('tabSpeakersBtn');
                const tabDubsync = document.getElementById('tabDubsync');
                const tabAudiobooks = document.getElementById('tabAudiobooks');
                const tabSpeakers = document.getElementById('tabSpeakers');

                const setActiveTab = (active) => {
                    const tabs = {
                        dubsync: {
                            btn: tabDubsyncBtn,
                            content: tabDubsync
                        },
                        audiobooks: {
                            btn: tabAudiobooksBtn,
                            content: tabAudiobooks
                        },
                        speakers: {
                            btn: tabSpeakersBtn,
                            content: tabSpeakers
                        }
                    };

                    Object.keys(tabs).forEach(key => {
                        const {
                            btn,
                            content
                        } = tabs[key];
                        if (!btn || !content) return;

                        if (key === active) {
                            content.classList.remove('hidden');
                            btn.classList.add('border-red-600', 'text-gray-900');
                            btn.classList.remove('border-transparent', 'text-gray-500');

                            // Load speakers when tab is opened
                            if (key === 'speakers') {
                                loadSpeakers();
                            }
                        } else {
                            content.classList.add('hidden');
                            btn.classList.add('border-transparent', 'text-gray-500');
                            btn.classList.remove('border-red-600', 'text-gray-900');
                        }
                    });
                };

                if (tabDubsyncBtn) {
                    tabDubsyncBtn.addEventListener('click', () => setActiveTab('dubsync'));
                }

                if (tabAudiobooksBtn) {
                    tabAudiobooksBtn.addEventListener('click', () => setActiveTab('audiobooks'));
                }

                if (tabSpeakersBtn) {
                    tabSpeakersBtn.addEventListener('click', () => setActiveTab('speakers'));
                }

                setActiveTab('dubsync');

                openBtns.forEach((btn) => {
                    if (!btn) return;
                    btn.addEventListener('click', () => {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
                });

                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    });
                }

                if (modal) {
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    });
                }

                const selectAll = document.getElementById('selectAllProjects');
                const checkboxes = Array.from(document.querySelectorAll('.project-checkbox'));
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
                const bulkDeleteForm = document.getElementById('bulkDeleteForm');
                const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');
                const bulkTranscriptBtn = document.getElementById('bulkTranscriptBtn');

                const updateBulkState = () => {
                    const checked = checkboxes.filter((cb) => cb.checked);
                    const checkedNew = checked.filter((cb) => {
                        const row = cb.closest('tr');
                        return row?.dataset?.status === 'new';
                    });
                    if (bulkDeleteBtn) {
                        bulkDeleteBtn.disabled = checked.length === 0;
                        bulkDeleteBtn.classList.toggle('opacity-60', checked.length === 0);
                        bulkDeleteBtn.classList.toggle('cursor-not-allowed', checked.length === 0);
                    }
                    if (bulkTranscriptBtn) {
                        bulkTranscriptBtn.disabled = checkedNew.length === 0;
                        bulkTranscriptBtn.classList.toggle('opacity-60', checkedNew.length === 0);
                        bulkTranscriptBtn.classList.toggle('cursor-not-allowed', checkedNew.length === 0);
                    }
                    if (selectAll) {
                        selectAll.checked = checked.length > 0 && checked.length === checkboxes.length;
                        selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
                    }
                };

                if (selectAll) {
                    selectAll.addEventListener('change', () => {
                        checkboxes.forEach((cb) => {
                            cb.checked = selectAll.checked;
                        });
                        updateBulkState();
                    });
                }

                checkboxes.forEach((cb) => {
                    cb.addEventListener('change', updateBulkState);
                });

                if (bulkDeleteBtn && bulkDeleteForm) {
                    bulkDeleteBtn.addEventListener('click', () => {
                        if (bulkDeleteBtn.disabled) return;
                        if (confirm('Delete selected projects?')) {
                            if (bulkDeleteInputs) {
                                bulkDeleteInputs.innerHTML = '';
                                checkboxes
                                    .filter((cb) => cb.checked)
                                    .forEach((cb) => {
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'project_ids[]';
                                        input.value = cb.dataset.projectId;
                                        bulkDeleteInputs.appendChild(input);
                                    });
                            }
                            bulkDeleteForm.submit();
                        }
                    });
                }

                if (bulkTranscriptBtn) {
                    bulkTranscriptBtn.addEventListener('click', async () => {
                        if (bulkTranscriptBtn.disabled) return;

                        const selected = checkboxes.filter((cb) => cb.checked);
                        const selectedNew = selected.filter((cb) => {
                            const row = cb.closest('tr');
                            return row?.dataset?.status === 'new';
                        });
                        if (selectedNew.length === 0) return;

                        bulkTranscriptBtn.disabled = true;
                        bulkTranscriptBtn.classList.add('opacity-60', 'cursor-not-allowed');

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content');

                        for (const cb of selectedNew) {
                            const row = cb.closest('tr');
                            const spinner = row?.querySelector('.row-spinner');
                            const statusEl = row?.querySelector('.project-status');
                            const url = row?.dataset?.transcriptUrl;

                            if (!url) continue;

                            if (spinner) {
                                spinner.classList.remove('hidden');
                                spinner.classList.add('inline-flex');
                            }
                            if (statusEl) {
                                statusEl.textContent = 'Processing';
                            }

                            try {
                                const response = await fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken || '',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({})
                                });

                                if (response.ok) {
                                    const data = await response.json();
                                    if (statusEl) {
                                        statusEl.textContent = data.status ? data.status.replace('_', ' ') :
                                            'Transcribed';
                                    }
                                } else {
                                    if (statusEl) {
                                        statusEl.textContent = 'Error';
                                    }
                                }
                            } catch (e) {
                                if (statusEl) {
                                    statusEl.textContent = 'Error';
                                }
                            } finally {
                                if (spinner) {
                                    spinner.classList.add('hidden');
                                    spinner.classList.remove('inline-flex');
                                }
                            }
                        }

                        updateBulkState();
                    });
                }

                const rowLinks = document.querySelectorAll('tr[data-href]');
                rowLinks.forEach((row) => {
                    row.addEventListener('click', (e) => {
                        const interactive = e.target.closest('a, button, input, form, label');
                        if (interactive) return;
                        const href = row.dataset.href;
                        if (href) {
                            window.location.href = href;
                        }
                    });
                });

                updateBulkState();

                // Handle Retry Transcript button
                const retryButtons = document.querySelectorAll('.retry-transcript-btn');
                retryButtons.forEach((btn) => {
                    btn.addEventListener('click', async (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const projectId = btn.dataset.projectId;
                        const transcriptUrl = btn.dataset.transcriptUrl;
                        const row = btn.closest('tr');
                        const statusEl = row?.querySelector('.project-status');
                        const spinner = row?.querySelector('.row-spinner');

                        if (!transcriptUrl) {
                            alert('Missing transcript URL');
                            return;
                        }

                        if (!confirm('Retry fetching transcript for this project?')) {
                            return;
                        }

                        btn.disabled = true;
                        btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Retrying...';

                        if (spinner) {
                            spinner.classList.remove('hidden');
                            spinner.classList.add('inline-flex');
                        }
                        if (statusEl) {
                            statusEl.textContent = 'Processing';
                        }

                        try {
                            const response = await fetch(transcriptUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')?.content || '',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({})
                            });

                            const data = await response.json();

                            if (response.ok && data.success) {
                                if (statusEl) {
                                    statusEl.textContent = data.status ? data.status.replace('_',
                                        ' ') : 'Transcribed';
                                }
                                // Remove retry button on success, reload page to show updated state
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                if (statusEl) {
                                    statusEl.textContent = 'Error';
                                }
                                alert('Failed to retry: ' + (data.error || 'Unknown error'));
                                btn.disabled = false;
                                btn.innerHTML = '<i class="ri-refresh-line"></i> Retry';
                            }
                        } catch (e) {
                            if (statusEl) {
                                statusEl.textContent = 'Error';
                            }
                            alert('Error retrying transcript: ' + e.message);
                            btn.disabled = false;
                            btn.innerHTML = '<i class="ri-refresh-line"></i> Retry';
                        } finally {
                            if (spinner) {
                                spinner.classList.add('hidden');
                                spinner.classList.remove('inline-flex');
                            }
                        }
                    });
                });
            });

            // ========== SPEAKER (MC) MANAGEMENT ==========
            const channelId = {{ $youtubeChannel->id }};
            const speakersUrl = "{{ route('youtube-channels.speakers.index', $youtubeChannel) }}";
            const speakerStoreUrl = "{{ route('youtube-channels.speakers.store', $youtubeChannel) }}";
            let speakersData = [];

            // Load speakers
            async function loadSpeakers() {
                const container = document.getElementById('speakersContainer');
                try {
                    const response = await fetch(speakersUrl);
                    const data = await response.json();

                    if (data.success) {
                        speakersData = data.speakers;
                        renderSpeakers(data.speakers);
                    } else {
                        container.innerHTML =
                            '<div class="col-span-full text-center py-8 text-red-500">Lỗi tải danh sách MC</div>';
                    }
                } catch (e) {
                    container.innerHTML = '<div class="col-span-full text-center py-8 text-red-500">Lỗi: ' + e.message +
                        '</div>';
                }
            }

            // Render speakers grid
            function renderSpeakers(speakers) {
                const container = document.getElementById('speakersContainer');

                if (speakers.length === 0) {
                    container.innerHTML = `
                        <div class="col-span-full text-center py-8 text-gray-400">
                            <div class="text-4xl mb-2">🎙️</div>
                            <p>Chưa có MC nào. Hãy thêm MC đầu tiên cho kênh!</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = speakers.map(speaker => `
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-200 ${!speaker.is_active ? 'opacity-60' : ''}">
                        <div class="relative bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center p-4" style="min-height: 160px;">
                            ${speaker.avatar_url 
                                ? `<img src="${speaker.avatar_url}" alt="${speaker.name}" class="w-24 h-24 object-cover rounded-full border-4 border-white shadow-lg">`
                                : `<div class="w-24 h-24 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white text-3xl shadow-lg border-4 border-white">
                                                    ${speaker.gender === 'male' ? '👨' : '👩'}
                                                   </div>`
                            }
                            ${!speaker.is_active ? '<span class="absolute top-2 right-2 bg-gray-500 text-white text-xs px-2 py-1 rounded">Tạm ẩn</span>' : ''}
                            ${speaker.lip_sync_enabled ? '<span class="absolute top-2 left-2 bg-purple-500 text-white text-xs px-2 py-1 rounded">🎬 Lip-sync</span>' : ''}
                        </div>
                        <div class="p-4">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">${speaker.name}</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                ${speaker.gender === 'male' ? '👨 Nam' : '👩 Nữ'}
                                ${speaker.default_voice_provider ? ' • ' + speaker.default_voice_provider.toUpperCase() : ''}
                            </p>
                            ${speaker.description ? `<p class="text-xs text-gray-600 mt-2 line-clamp-2">${speaker.description}</p>` : ''}
                            <p class="text-xs text-purple-600 mt-2">📚 ${speaker.audiobooks_count} audiobooks</p>
                            
                            <div class="flex gap-2 mt-4">
                                <button onclick="editSpeaker(${speaker.id})" class="flex-1 text-center px-3 py-1.5 bg-purple-600 text-white rounded text-xs font-medium hover:bg-purple-700">
                                    ✏️ Sửa
                                </button>
                                <button onclick="toggleSpeakerStatus(${speaker.id})" class="px-3 py-1.5 ${speaker.is_active ? 'bg-gray-200 text-gray-800 hover:bg-gray-300' : 'bg-green-100 text-green-700 hover:bg-green-200'} rounded text-xs font-medium">
                                    ${speaker.is_active ? '🙈' : '👁️'}
                                </button>
                                <button onclick="deleteSpeaker(${speaker.id}, '${speaker.name}')" class="px-3 py-1.5 bg-red-100 text-red-700 rounded text-xs font-medium hover:bg-red-200">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            // Open speaker modal
            function openSpeakerModal(speakerId = null) {
                const modal = document.getElementById('speakerModal');
                const form = document.getElementById('speakerForm');
                const title = document.getElementById('speakerModalTitle');
                const submitBtn = document.getElementById('speakerSubmitBtn');

                // Reset form
                form.reset();
                document.getElementById('speakerId').value = '';
                document.getElementById('speakerAvatarPreview').classList.add('hidden');
                document.getElementById('speakerFormStatus').innerHTML = '';
                document.getElementById('lipSyncSettings').classList.add('hidden');

                if (speakerId) {
                    // Edit mode
                    const speaker = speakersData.find(s => s.id === speakerId);
                    if (speaker) {
                        title.textContent = 'Chỉnh sửa MC: ' + speaker.name;
                        submitBtn.textContent = '💾 Cập nhật MC';
                        document.getElementById('speakerId').value = speaker.id;
                        document.getElementById('speakerName').value = speaker.name;
                        document.getElementById('speakerDescription').value = speaker.description || '';
                        document.getElementById('speakerVoiceProvider').value = speaker.default_voice_provider || '';
                        document.getElementById('speakerVoiceStyle').value = speaker.voice_style || '';
                        document.getElementById('speakerLipSyncEnabled').checked = speaker.lip_sync_enabled;

                        const genderRadio = document.querySelector(`input[name="gender"][value="${speaker.gender}"]`);
                        if (genderRadio) genderRadio.checked = true;

                        if (speaker.avatar_url) {
                            const preview = document.getElementById('speakerAvatarPreview');
                            preview.querySelector('img').src = speaker.avatar_url;
                            preview.classList.remove('hidden');
                        }

                        if (speaker.lip_sync_enabled) {
                            document.getElementById('lipSyncSettings').classList.remove('hidden');
                            if (speaker.lip_sync_settings) {
                                document.getElementById('lipSyncSensitivity').value = speaker.lip_sync_settings.sensitivity ||
                                    0.5;
                                document.getElementById('lipSyncSensitivityValue').textContent = speaker.lip_sync_settings
                                    .sensitivity || 0.5;
                            }
                        }

                        // Load voice options after setting provider and gender
                        setTimeout(() => {
                            updateSpeakerVoiceOptions();
                            // Set the selected voice after options are loaded
                            setTimeout(() => {
                                if (speaker.default_voice_name) {
                                    document.getElementById('speakerVoiceName').value = speaker
                                        .default_voice_name;
                                }
                            }, 300);
                        }, 100);
                    }
                } else {
                    // Add mode
                    title.textContent = 'Thêm MC mới';
                    submitBtn.textContent = '💾 Lưu MC';
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            // Close speaker modal
            function closeSpeakerModal() {
                const modal = document.getElementById('speakerModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // Edit speaker
            function editSpeaker(speakerId) {
                openSpeakerModal(speakerId);
            }

            // Toggle speaker status
            async function toggleSpeakerStatus(speakerId) {
                try {
                    const response = await fetch(`/youtube-channels/${channelId}/speakers/${speakerId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        loadSpeakers();
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể thay đổi trạng thái'));
                    }
                } catch (e) {
                    alert('Lỗi: ' + e.message);
                }
            }

            // Delete speaker
            async function deleteSpeaker(speakerId, speakerName) {
                if (!confirm(`Bạn có chắc muốn xóa MC "${speakerName}"?`)) return;

                try {
                    const response = await fetch(`/youtube-channels/${channelId}/speakers/${speakerId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        loadSpeakers();
                    } else {
                        alert('Lỗi: ' + (data.message || 'Không thể xóa MC'));
                    }
                } catch (e) {
                    alert('Lỗi: ' + e.message);
                }
            }

            // Handle speaker form submit
            document.getElementById('speakerForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const speakerId = document.getElementById('speakerId').value;
                const statusEl = document.getElementById('speakerFormStatus');
                const submitBtn = document.getElementById('speakerSubmitBtn');

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin inline-block mr-2">⏳</span> Đang lưu...';
                statusEl.innerHTML = '<span class="text-blue-600">Đang xử lý...</span>';

                try {
                    let url = speakerStoreUrl;
                    let method = 'POST';

                    if (speakerId) {
                        url = `/youtube-channels/${channelId}/speakers/${speakerId}`;
                        formData.append('_method', 'PUT');
                    }

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                '',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        statusEl.innerHTML = '<span class="text-green-600">✅ ' + data.message + '</span>';
                        setTimeout(() => {
                            closeSpeakerModal();
                            loadSpeakers();
                        }, 1000);
                    } else {
                        statusEl.innerHTML = '<span class="text-red-600">❌ ' + (data.message ||
                            'Lỗi không xác định') + '</span>';
                    }
                } catch (e) {
                    statusEl.innerHTML = '<span class="text-red-600">❌ Lỗi: ' + e.message + '</span>';
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = speakerId ? '💾 Cập nhật MC' : '💾 Lưu MC';
                }
            });

            // Avatar preview
            document.getElementById('speakerAvatar')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('speakerAvatarPreview');
                        preview.querySelector('img').src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Lip sync toggle
            document.getElementById('speakerLipSyncEnabled')?.addEventListener('change', function() {
                const settings = document.getElementById('lipSyncSettings');
                if (this.checked) {
                    settings.classList.remove('hidden');
                } else {
                    settings.classList.add('hidden');
                }
            });

            // Lip sync sensitivity slider
            document.getElementById('lipSyncSensitivity')?.addEventListener('input', function() {
                document.getElementById('lipSyncSensitivityValue').textContent = this.value;
            });

            // Voice selection logic
            let speakerVoiceCache = {};

            // Provider change for speaker
            document.getElementById('speakerVoiceProvider')?.addEventListener('change', function() {
                updateSpeakerVoiceOptions();
            });

            // Gender change for speaker
            document.querySelectorAll('input[name="gender"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    updateSpeakerVoiceOptions();
                });
            });

            // Update speaker voice options
            async function updateSpeakerVoiceOptions() {
                const voiceSelect = document.getElementById('speakerVoiceName');
                const provider = document.getElementById('speakerVoiceProvider').value;
                const gender = document.querySelector('input[name="gender"]:checked')?.value || 'female';

                if (!provider) {
                    voiceSelect.innerHTML = '<option value="">-- Chọn TTS Provider trước --</option>';
                    return;
                }

                voiceSelect.innerHTML = '<option value="">⏳ Đang tải...</option>';

                try {
                    const voices = await fetchSpeakerVoices(gender, provider);
                    voiceSelect.innerHTML = '<option value="">-- Chọn giọng --</option>';

                    for (const [voiceCode, voiceLabel] of Object.entries(voices)) {
                        const option = document.createElement('option');
                        option.value = voiceCode;
                        option.textContent = voiceLabel;
                        voiceSelect.appendChild(option);
                    }
                } catch (error) {
                    voiceSelect.innerHTML = '<option value="">-- Lỗi tải giọng --</option>';
                }
            }

            async function fetchSpeakerVoices(gender, provider) {
                const cacheKey = `${provider}:${gender}`;
                if (speakerVoiceCache[cacheKey]) {
                    return speakerVoiceCache[cacheKey];
                }

                const response = await fetch(`/get-available-voices?gender=${gender}&provider=${provider}`);
                const data = await response.json();

                if (data.success) {
                    speakerVoiceCache[cacheKey] = data.voices[gender] || {};
                    return speakerVoiceCache[cacheKey];
                }
                return {};
            }

            // Close modal on backdrop click
            document.getElementById('speakerModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeSpeakerModal();
                }
            });
        </script>
    @endsection

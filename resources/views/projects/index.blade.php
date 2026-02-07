@extends('layouts.app')

@section('content')
    <div class="py-12" style="background-color: #f0f0f0; min-height: 400px;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Debug info -->
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 rounded">
                <strong>Debug:</strong> Projects count = {{ $projects->total() ?? 'N/A' }} |
                Current page items = {{ $projects->count() ?? 'N/A' }} |
                User ID = {{ auth()->id() ?? 'NOT LOGGED IN' }}
            </div>

            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">
                    {{ __('Projects Management') }}
                </h2>
                <a href="{{ route('projects.create') }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    + New Project
                </a>
            </div>

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
                    @if ($projects->count() > 0)
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="text-sm text-gray-600">View:</div>
                                <div class="inline-flex rounded-lg border border-gray-200 overflow-hidden">
                                    <button id="listViewBtn"
                                        class="px-4 py-2 text-sm font-medium bg-white text-gray-700 hover:bg-gray-50">
                                        List
                                    </button>
                                    <button id="cardViewBtn"
                                        class="px-4 py-2 text-sm font-medium bg-gray-100 text-gray-900 hover:bg-gray-200">
                                        Cards
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <label for="perPageSelect" class="text-sm text-gray-600">Items per page:</label>
                                <select id="perPageSelect"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="10" @if (request('per_page') == 10 || request('per_page') === null) selected @endif>10</option>
                                    <option value="15" @if (request('per_page') == 15) selected @endif>15</option>
                                    <option value="25" @if (request('per_page') == 25) selected @endif>25</option>
                                    <option value="50" @if (request('per_page') == 50) selected @endif>50</option>
                                    <option value="100" @if (request('per_page') == 100) selected @endif>100
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Card View (default) -->
                        <div id="projectsCardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($projects as $project)
                                <div class="project-item bg-white border border-gray-200 rounded-lg overflow-hidden shadow hover:shadow-lg transition duration-300"
                                    data-project-id="{{ $project->id }}">
                                    <!-- Thumbnail -->
                                    <div class="relative h-40 bg-gray-200 overflow-hidden">
                                        @if ($project->youtube_thumbnail)
                                            <img src="{{ $project->youtube_thumbnail }}" alt="Project thumbnail"
                                                class="w-full h-full object-cover">
                                        @elseif ($project->thumbnail_path)
                                            <img src="{{ asset($project->thumbnail_path) }}" alt="Project thumbnail"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-300 to-gray-400">
                                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @switch($project->status)
                                                    @case('pending')
                                                        bg-gray-100 text-gray-800
                                                        @break
                                                    @case('processing')
                                                        bg-blue-100 text-blue-800
                                                        @break
                                                    @case('transcribed')
                                                        bg-cyan-100 text-cyan-800
                                                        @break
                                                    @case('translated')
                                                        bg-indigo-100 text-indigo-800
                                                        @break
                                                    @case('tts_generated')
                                                        bg-purple-100 text-purple-800
                                                        @break
                                                    @case('aligned')
                                                        bg-pink-100 text-pink-800
                                                        @break
                                                    @case('merged')
                                                        bg-orange-100 text-orange-800
                                                        @break
                                                    @case('completed')
                                                        bg-green-100 text-green-800
                                                        @break
                                                    @default
                                                        bg-gray-100 text-gray-800
                                                @endswitch">
                                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-4">
                                        <h3 class="text-sm font-semibold text-gray-900 truncate mb-2">
                                            {{ $project->youtube_title ?? $project->video_id }}
                                        </h3>

                                        <p class="text-xs text-gray-600 mb-2 max-h-10 overflow-hidden">
                                            {{ $project->youtube_description ?? 'No description available.' }}
                                        </p>

                                        <p class="text-xs text-gray-500 mb-3">
                                            <span class="font-medium">Video ID:</span> {{ $project->video_id }}
                                            @if ($project->youtube_duration)
                                                <span class="ml-2 font-medium">Duration:</span>
                                                {{ $project->youtube_duration }}
                                            @endif
                                        </p>

                                        <p class="text-xs text-gray-500 mb-4">
                                            <span class="font-medium">Created:</span>
                                            {{ $project->created_at->format('d/m/Y H:i') }}
                                        </p>

                                        <p class="text-xs text-gray-600 mb-3 truncate">
                                            <a href="{{ $project->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                class="text-red-600 hover:text-red-800 hover:underline">
                                                View on YouTube
                                            </a>
                                        </p>

                                        <div class="flex gap-2 pt-3 border-t border-gray-200">
                                            <a href="{{ route('projects.show', $project->id) }}"
                                                class="flex-1 text-center text-sm px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                                View
                                            </a>
                                            <a href="{{ route('projects.edit', $project->id) }}"
                                                class="flex-1 text-center text-sm px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                                                Edit
                                            </a>
                                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                                class="delete-project-form flex-1"
                                                onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full text-sm px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- List View -->
                        <div id="projectsListView" class="hidden space-y-3">
                            @foreach ($projects as $project)
                                <div class="project-item bg-white border border-gray-200 rounded-lg p-4 flex flex-wrap gap-4"
                                    data-project-id="{{ $project->id }}">
                                    <div class="w-40 h-24 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                        @if ($project->youtube_thumbnail)
                                            <img src="{{ $project->youtube_thumbnail }}" alt="Project thumbnail"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-300 to-gray-400">
                                                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <h3 class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ $project->youtube_title ?? 'No title available.' }}
                                                </h3>
                                                <p class="text-xs text-gray-600 mt-1 max-h-10 overflow-hidden">
                                                    {{ $project->youtube_description ?? 'No description available.' }}
                                                </p>
                                            </div>
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full flex-shrink-0
                                                @switch($project->status)
                                                    @case('pending')
                                                        bg-gray-100 text-gray-800
                                                        @break
                                                    @case('processing')
                                                        bg-blue-100 text-blue-800
                                                        @break
                                                    @case('transcribed')
                                                        bg-cyan-100 text-cyan-800
                                                        @break
                                                    @case('translated')
                                                        bg-indigo-100 text-indigo-800
                                                        @break
                                                    @case('tts_generated')
                                                        bg-purple-100 text-purple-800
                                                        @break
                                                    @case('aligned')
                                                        bg-pink-100 text-pink-800
                                                        @break
                                                    @case('merged')
                                                        bg-orange-100 text-orange-800
                                                        @break
                                                    @case('completed')
                                                        bg-green-100 text-green-800
                                                        @break
                                                    @default
                                                        bg-gray-100 text-gray-800
                                                @endswitch">
                                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                            </span>
                                        </div>

                                        <p class="text-xs text-gray-500 mt-2">
                                            <span class="font-medium">Video ID:</span> {{ $project->video_id }}
                                            @if ($project->youtube_duration)
                                                <span class="ml-2 font-medium">Duration:</span>
                                                {{ $project->youtube_duration }}
                                            @endif
                                            <span class="ml-2 font-medium">Created:</span>
                                            {{ $project->created_at->format('d/m/Y H:i') }}
                                        </p>

                                        <p class="text-xs text-gray-600 mt-1 truncate">
                                            <a href="{{ $project->youtube_url }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="text-red-600 hover:text-red-800 hover:underline">
                                                View on YouTube
                                            </a>
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('projects.show', $project->id) }}"
                                            class="text-sm px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                            View
                                        </a>
                                        <a href="{{ route('projects.edit', $project->id) }}"
                                            class="text-sm px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                            class="delete-project-form" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-sm px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $projects->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 13h6m-3-3v6m-9 1V5a2 2 0 012-2h6.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new project.</p>
                            <div class="mt-6">
                                <a href="{{ route('projects.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    New Project
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
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

            const deleteForms = document.querySelectorAll('.delete-project-form');
            deleteForms.forEach((form) => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (!confirm('Are you sure?')) {
                        return;
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const success = response.ok || response.status === 204 || response
                            .status === 302 || response.redirected || response.status === 404;

                        if (!success) {
                            throw new Error('Delete failed');
                        }

                        const projectItem = form.closest('.project-item');
                        if (projectItem) {
                            projectItem.remove();
                        }

                        // Ensure UI refreshes fully
                        window.location.reload();
                    } catch (error) {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                        }
                        // Fallback: refresh to reflect server state even if response not OK
                        window.location.reload();
                    }
                });
            });

            const listBtn = document.getElementById('listViewBtn');
            const cardBtn = document.getElementById('cardViewBtn');
            const listView = document.getElementById('projectsListView');
            const cardView = document.getElementById('projectsCardView');

            if (!listBtn || !cardBtn || !listView || !cardView) return;

            const setView = (mode) => {
                const isList = mode === 'list';
                listView.classList.toggle('hidden', !isList);
                cardView.classList.toggle('hidden', isList);
                listBtn.classList.toggle('bg-gray-100', isList);
                listBtn.classList.toggle('text-gray-900', isList);
                listBtn.classList.toggle('bg-white', !isList);
                listBtn.classList.toggle('text-gray-700', !isList);
                cardBtn.classList.toggle('bg-gray-100', !isList);
                cardBtn.classList.toggle('text-gray-900', !isList);
                cardBtn.classList.toggle('bg-white', isList);
                cardBtn.classList.toggle('text-gray-700', isList);
                localStorage.setItem('projectsViewMode', mode);
            };

            const savedMode = localStorage.getItem('projectsViewMode') || 'card';
            setView(savedMode);

            listBtn.addEventListener('click', () => setView('list'));
            cardBtn.addEventListener('click', () => setView('card'));
        })();
    </script>
@endsection

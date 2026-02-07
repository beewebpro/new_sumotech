@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">
                    {{ __('Project Details') }}
                </h2>
                <div class="space-x-3">
                    <a href="{{ route('projects.edit', $project->id) }}"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Edit
                    </a>
                    <a href="{{ route('projects.index') }}"
                        class="bg-black hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Back
                    </a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Project Info -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Project Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Video ID</label>
                                <p class="text-gray-900 font-mono">{{ $project->video_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">YouTube URL</label>
                                <a href="{{ $project->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                    class="text-red-600 hover:underline break-all">
                                    {{ $project->youtube_url }}
                                </a>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">YouTube Title</label>
                                <p class="text-teal-600"><span
                                        class="px-1.5 py-0.5 rounded text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200">EN</span>
                                    {{ $project->youtube_title ?? 'N/A' }}</p>
                                @if ($project->youtube_title_vi)
                                    <p class="text-gray-700 italic mt-1"><span
                                            class="px-1.5 py-0.5 rounded text-xs font-semibold text-white bg-red-600">VI</span>
                                        {{ $project->youtube_title_vi }}</p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">YouTube Duration</label>
                                <p class="text-gray-900">{{ $project->youtube_duration ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">YouTube Description</label>
                                <p class="text-teal-600 bg-gray-50 p-2 rounded line-clamp-3">
                                    <span
                                        class="px-1.5 py-0.5 rounded text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200">EN</span>
                                    {{ $project->youtube_description ?? 'N/A' }}
                                </p>
                                @if ($project->youtube_description_vi)
                                    <p class="text-gray-600 italic bg-gray-50 p-2 rounded line-clamp-3 mt-2">
                                        <span
                                            class="px-1.5 py-0.5 rounded text-xs font-semibold text-white bg-red-600">VI</span>
                                        {{ $project->youtube_description_vi }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span
                                    class="px-3 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    @switch($project->status)
                                        @case('completed')
                                            bg-green-100 text-green-800
                                            @break
                                        @case('transcribed')
                                        @case('translated')
                                        @case('tts_generated')
                                        @case('aligned')
                                        @case('merged')
                                            bg-yellow-100 text-yellow-800
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                                    <p class="text-gray-900">{{ $project->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Updated At</label>
                                    <p class="text-gray-900">{{ $project->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Processing Status</h3>

                        <div class="space-y-3">
                            @php
                                $steps = [
                                    'transcribed' => 'Transcript Extracted',
                                    'translated' => 'Translated to Vietnamese',
                                    'tts_generated' => 'TTS Audio Generated',
                                    'aligned' => 'Audio Aligned',
                                    'merged' => 'Audio Merged',
                                    'completed' => 'Project Completed',
                                ];
                            @endphp

                            @foreach ($steps as $status => $label)
                                <div class="flex items-center">
                                    @if (in_array($project->status, array_keys($steps)) &&
                                            array_search($project->status, array_keys($steps)) >= array_search($status, array_keys($steps)))
                                        <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    <span
                                        class="ml-2 text-sm @if (in_array($project->status, array_keys($steps)) &&
                                                array_search($project->status, array_keys($steps)) >= array_search($status, array_keys($steps))) text-gray-900 font-medium @else text-gray-500 @endif">
                                        {{ $label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segments Preview -->
            @if ($project->segments && count($project->segments) > 0)
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Transcript Segments ({{ count($project->segments) }})
                        </h3>

                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach ($project->segments as $index => $segment)
                                <div class="border rounded-lg p-3 bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 mb-1">
                                                {{ number_format($segment['start'] ?? 0, 2) }}s -
                                                {{ number_format(($segment['start'] ?? 0) + ($segment['duration'] ?? 0), 2) }}s
                                            </p>
                                            <p class="text-sm text-gray-900">{{ Str::limit($segment['text'], 100) }}
                                            </p>
                                        </div>
                                        <span
                                            class="ml-2 px-2 py-1 bg-black text-white text-xs rounded">{{ $index + 1 }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Delete Section -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-t border-red-200">
                    <h3 class="text-lg font-semibold text-red-600 mb-4">Danger Zone</h3>
                    <p class="text-gray-600 mb-4">Delete this project and all associated files. This action cannot be
                        undone.</p>

                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="inline"
                        onsubmit="return confirm('Are you absolutely sure? This will delete all project data and files.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                            Delete Project
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

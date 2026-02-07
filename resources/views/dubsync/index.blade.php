<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('DubSync - Video Dubbing Workflow') }}
            </h2>
            <a href="{{ route('projects.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                Manage Projects
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- New Project Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Start New DubSync Project</h3>

                    <div id="newProjectForm">
                        <div class="mb-4">
                            <label for="youtubeUrl" class="block text-sm font-medium text-gray-700 mb-2">
                                YouTube URL
                            </label>
                            <input type="text" id="youtubeUrl"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                        <button id="processYoutubeBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                            <span id="processYoutubeBtnText">Start Processing</span>
                            <span id="processYoutubeBtnLoader" class="hidden">
                                <svg class="animate-spin h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>

                    <!-- Progress Steps -->
                    <div id="progressSection" class="mt-6 hidden">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-3">Processing Status</h4>
                            <div class="space-y-2">
                                <div id="step1">
                                    <div class="flex items-center">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                            <span class="text-xs">1</span>
                                        </div>
                                        <span class="text-sm">Extract Transcript from YouTube</span>
                                    </div>
                                    <!-- Progress bar for step 1 -->
                                    <div class="ml-9 mt-2 hidden" id="step1-progress">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                                                id="step1-progress-bar" style="width: 0%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1" id="step1-progress-text">0%</div>
                                    </div>
                                </div>
                                <div class="flex items-center" id="step2">
                                    <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                        <span class="text-xs">2</span>
                                    </div>
                                    <span class="text-sm">Translate to Vietnamese</span>
                                </div>
                                <div class="flex items-center" id="step3">
                                    <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                        <span class="text-xs">3</span>
                                    </div>
                                    <span class="text-sm">Generate TTS Voice</span>
                                </div>
                                <div class="flex items-center" id="step4">
                                    <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                        <span class="text-xs">4</span>
                                    </div>
                                    <span class="text-sm">Align Audio Timing</span>
                                </div>
                                <div class="flex items-center" id="step5">
                                    <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                        <span class="text-xs">5</span>
                                    </div>
                                    <span class="text-sm">Merge Audio</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Segments Editor -->
                    <div id="segmentsEditor" class="mt-6 hidden">
                        <h4 class="font-semibold mb-3">Edit Segments</h4>
                        <div id="segmentsList" class="space-y-3">
                            <!-- Segments will be loaded here dynamically -->
                        </div>

                        <div class="mt-4 flex space-x-3">
                            <button id="translateBtn"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                                Translate to Vietnamese
                            </button>
                            <button id="generateTTSBtn"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 hidden">
                                Generate TTS Voice
                            </button>
                            <button id="alignTimingBtn"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 hidden">
                                Align Timing
                            </button>
                            <button id="mergeAudioBtn"
                                class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 hidden">
                                Merge Audio
                            </button>
                        </div>
                    </div>

                    <!-- Export Section -->
                    <div id="exportSection" class="mt-6 hidden">
                        <h4 class="font-semibold mb-3">Export Files</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Formats:</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="export-format mr-2" value="srt" checked>
                                        <span class="text-sm">SRT (SubRip Subtitle)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="export-format mr-2" value="vtt" checked>
                                        <span class="text-sm">VTT (WebVTT)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="export-format mr-2" value="audio_wav" checked>
                                        <span class="text-sm">WAV Audio</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="export-format mr-2" value="audio_mp3" checked>
                                        <span class="text-sm">MP3 Audio</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="export-format mr-2" value="json" checked>
                                        <span class="text-sm">JSON Project File</span>
                                    </label>
                                </div>
                            </div>
                            <button id="exportBtn"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                                Export Files
                            </button>
                        </div>

                        <div id="downloadLinks" class="mt-4 hidden">
                            <h5 class="font-semibold mb-2">Download:</h5>
                            <div id="downloadLinksList" class="space-y-2">
                                <!-- Download links will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Projects -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Completed Projects</h3>

                    @if ($projects->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Video ID
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            YouTube URL
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Segments
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($projects as $project)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $project->video_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <a href="{{ $project->youtube_url }}" target="_blank"
                                                    class="text-blue-600 hover:underline">
                                                    {{ Str::limit($project->youtube_url, 50) }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $project->segments ? count($project->segments) : 0 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $project->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('projects.show', $project->id) }}"
                                                    class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                                <button class="text-red-600 hover:text-red-900 delete-project"
                                                    data-project-id="{{ $project->id }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $projects->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No completed projects yet. Start by creating a new
                            project above!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/dubsync.js') }}?v={{ time() }}"></script>
    @endpush
</x-app-layout>

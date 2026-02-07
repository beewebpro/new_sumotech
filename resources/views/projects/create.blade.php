@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">
                    {{ __('DubSync - Video Dubbing Workflow') }}
                </h2>
                <a href="{{ route('projects.index') }}"
                    class="bg-black hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Manage Projects
                </a>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- New Project Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Start New DubSync Project</h3>

                    <div id="newProjectForm">
                        @if ($youtubeChannel)
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="text-sm text-gray-700">
                                    <span class="font-semibold text-red-700">Linked Channel:</span>
                                    {{ $youtubeChannel->title }}
                                </div>
                                <div class="text-xs text-gray-600 mt-1">Channel ID: {{ $youtubeChannel->channel_id }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">This channel is locked for this project.</div>
                                <input type="hidden" id="youtubeChannelId" value="{{ $youtubeChannel->id }}">
                            </div>
                        @endif
                        <div class="mb-4">
                            <label for="youtubeUrl" class="block text-sm font-medium text-gray-700 mb-2">
                                YouTube URL
                            </label>
                            <input type="text" id="youtubeUrl"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                        <button id="processYoutubeBtn"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                            <span id="processYoutubeBtnText">Start Processing</span>
                            <span id="processYoutubeBtnLoader" class="hidden">
                                <svg class="animate-spin h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
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
                            <!-- Timer display will be inserted here -->
                            <div id="transcriptTimerContainer" class="mb-3"></div>
                            <div class="space-y-2">
                                <div id="step1">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                            <span class="text-xs">1</span>
                                        </div>
                                        <div>
                                            <span class="text-sm">Extract Transcript & Segmentation</span>
                                            {{-- <p class="text-xs text-gray-500 mt-0.5">I will get raw transcript and create segments for you...</p> --}}
                                        </div>
                                    </div>
                                    <!-- Progress bar for step 1 -->
                                    <div class="ml-9 mt-2 hidden" id="step1-progress">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-red-600 h-2.5 rounded-full transition-all duration-300"
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

                    <!-- YouTube Video Information -->
                    <div id="youtubeInfoSection"
                        class="mt-6 hidden bg-gradient-to-r from-red-50 to-pink-50 rounded-lg border border-red-200 overflow-hidden">
                        <div class="flex flex-col md:flex-row gap-4 p-6">
                            <!-- Thumbnail -->
                            <div class="flex-shrink-0">
                                <img id="videoThumbnail"
                                    class="w-40 h-24 rounded-lg object-cover shadow-md border border-red-200" src=""
                                    alt="Video thumbnail">
                            </div>

                            <!-- Video Details -->
                            <div class="flex-1">
                                <div class="mb-2">
                                    <h3 id="videoTitle" class="text-lg font-bold text-teal-600 mb-0.5"><span
                                            class="px-1.5 py-0.5 rounded text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200">EN</span>
                                        Video Title
                                    </h3>
                                    <h4 id="videoTitleVi" class="text-md text-gray-600 italic hidden"><span
                                            class="px-1.5 py-0.5 rounded text-xs font-semibold text-white bg-red-600">VI</span>
                                        Tiêu đề tiếng
                                        Việt</h4>
                                </div>
                                <div class="mb-3">
                                    <p id="videoDescription" class="text-sm text-teal-600 line-clamp-2"><span
                                            class="px-1.5 py-0.5 rounded text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200">EN</span>
                                        Video
                                        description will appear here...</p>
                                    <p id="videoDescriptionVi"
                                        class="text-sm text-gray-600 italic line-clamp-2 hidden mt-1"><span
                                            class="px-1.5 py-0.5 rounded text-xs font-semibold text-white bg-red-600">VI</span>
                                        Mô tả tiếng
                                        Việt</p>
                                </div>
                                <div class="flex items-center gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Duration:</span>
                                        <span id="videoDuration" class="font-semibold text-gray-900 ml-1">--:--</span>
                                    </div>
                                    <div>
                                        <a id="videoLink" href="" target="_blank"
                                            class="text-red-600 hover:text-red-700 font-semibold flex items-center gap-1">
                                            Watch on YouTube
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Processing Status Indicator -->
                    <div id="aiProcessingStatus" class="mt-6 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center gap-4">
                                <!-- Spinner -->
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-12 h-12 rounded-full border-4 border-blue-200 border-t-blue-600 animate-spin">
                                    </div>
                                </div>

                                <!-- Status Text -->
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-1">Đang xử lý AI Segmentation...</h4>
                                    <p id="aiStatusMessage" class="text-sm text-gray-600 mb-2">Đang bắt đầu...</p>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div id="aiProgressBar"
                                            class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                            style="width: 0%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <span id="aiProgressPercent">0</span>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Segments Editor with Tabs -->
                    <div id="segmentsEditor" class="mt-6 hidden pb-32">
                        <!-- Tab Navigation -->
                        <div class="mb-4 border-b border-gray-200">
                            <div class="flex gap-4">
                                <button
                                    class="segment-tab-btn active px-4 py-2 text-sm font-medium text-gray-900 border-b-2 border-red-600"
                                    data-tab="edit-segments">
                                    Edit Segments
                                </button>
                                <button
                                    class="segment-tab-btn px-4 py-2 text-sm font-medium text-gray-600 border-b-2 border-transparent hover:border-gray-300"
                                    data-tab="full-transcript">
                                    Full Transcript
                                </button>
                            </div>
                        </div>

                        <!-- Edit Segments Tab -->
                        <div id="tab-edit-segments" class="segment-tab-content">
                            <h4 class="font-semibold mb-3">Edit Segments</h4>
                            <div class="mb-3 flex flex-wrap items-center gap-3">
                                <label class="flex items-center text-sm text-gray-700">
                                    <input type="checkbox" id="selectAllSegments" class="mr-2">
                                    Chọn tất cả
                                </label>
                                <button id="saveSegmentsBtn"
                                    class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    💾 Save Segments
                                </button>
                            </div>
                            <div id="segmentsList" class="space-y-3">
                                <!-- Segments will be loaded here dynamically -->
                            </div>
                        </div>

                        <!-- Full Transcript Tab -->
                        <div id="tab-full-transcript" class="segment-tab-content hidden">
                            <h4 class="font-semibold mb-3">Full Transcript</h4>
                            <div id="fullTranscriptContainer"
                                class="bg-white p-6 rounded-lg border border-gray-200 max-h-96 overflow-y-auto text-gray-800 leading-relaxed whitespace-pre-wrap">
                                <!-- Full transcript will be displayed here -->
                                <p class="text-gray-500 text-sm">Transcript will appear here...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Command Bar -->
                    <div id="floatingCommandBar"
                        class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-2xl hidden z-50">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                            <!-- Progress Bar -->
                            <div id="progressContainer" class="hidden mb-3">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Đang dịch...</span>
                                    <span id="progressPercent" class="text-sm font-medium text-green-600">0%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div id="progressBar"
                                        class="bg-green-600 h-2 rounded-full transition-all duration-300"
                                        style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 items-center">
                                <span class="text-sm font-medium text-gray-700">Actions:</span>

                                <!-- Provider Selector -->
                                <select id="translationProvider"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option value="google">Google Translate</option>
                                    <option value="openai">OpenAI (GPT)</option>
                                </select>

                                <button id="translateBtn"
                                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                                    Translate to Vietnamese
                                </button>
                                <!-- Clear/Reset button to allow editing translated content -->
                                <button id="clearTranslationBtn"
                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200"
                                    title="Clear translated content to edit and re-translate">
                                    Clear Translation
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

            <!-- YouTube Channel Reference -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">YouTube Channel Reference</h3>
                    <div class="mb-4">
                        <label for="youtubeChannelUrl" class="block text-sm font-medium text-gray-700 mb-2">
                            Channel URL
                        </label>
                        <input type="text" id="youtubeChannelUrl"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="https://www.youtube.com/@AstrumEarth">
                        <p class="text-xs text-gray-500 mt-2">Supports @handle or /channel/ ID URLs.</p>
                    </div>

                    <button id="fetchChannelVideosBtn" data-endpoint="{{ route('youtube.channel.videos') }}"
                        class="bg-gray-900 hover:bg-gray-800 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        Fetch Channel Videos
                    </button>

                    <div id="channelFetchStatus" class="mt-4 text-sm text-gray-600 hidden"></div>

                    <div id="channelInfo" class="mt-6 hidden">
                        <div class="flex items-center gap-3 mb-4">
                            <img id="channelThumbnail" src="" alt="Channel thumbnail"
                                class="w-12 h-12 rounded-full object-cover border">
                            <div>
                                <div id="channelTitle" class="text-sm font-semibold text-gray-900"></div>
                                <div id="channelIdText" class="text-xs text-gray-500"></div>
                            </div>
                        </div>
                    </div>

                    <div id="channelVideosList" class="mt-4 hidden">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Latest Videos</h4>
                        <div class="space-y-3" id="channelVideosContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/dubsync.js') }}?v={{ time() }}"></script>
    @endpush
@endsection

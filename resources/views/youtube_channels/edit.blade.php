@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">
                    {{ __('Edit YouTube Channel') }}
                </h2>
                <a href="{{ route('youtube-channels.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Back to list
                </a>
            </div>

            {{-- OAuth Disconnect Form (Outside Main Form) --}}
            <div id="oauthDisconnectFormContainer"></div>

            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                {{-- Flash messages --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        <p class="font-semibold text-sm mb-2">❌ Có lỗi xảy ra:</p>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form id="channelEditForm" method="POST" action="{{ route('youtube-channels.update', $youtubeChannel) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('youtube_channels._form', ['channel' => $youtubeChannel])

                            {{-- Final Save Button --}}
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div>
                                        <p class="text-sm font-semibold text-green-900">✅ Hoàn tất chỉnh sửa</p>
                                        <p class="text-xs text-green-700">Nhấn "Update Channel" để lưu tất cả thay đổi</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('youtube-channels.index') }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200">
                                            Cancel
                                        </a>
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 shadow-md">
                                            ✅ Update Channel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Floating Save Button --}}
        <div class="fixed bottom-6 right-6 z-50">
            <button type="submit" form="channelEditForm"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-full shadow-2xl transition duration-200 flex items-center gap-2 hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Save Changes</span>
            </button>
        </div>

        {{-- OAuth Disconnect Form (Outside Main Form to Avoid Nesting) --}}
        <form id="oauthDisconnectForm" method="POST" action="{{ route('youtube-channels.oauth.disconnect', $youtubeChannel) }}" style="display: none;">
            @csrf
        </form>

        {{-- Debug Script --}}
        <script>
            // Function to handle OAuth disconnect
            function disconnectYoutubeOAuth() {
                if (confirm('Bạn có chắc muốn ngắt kết nối YouTube API?')) {
                    document.getElementById('oauthDisconnectForm').submit();
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('channelEditForm');
                const submitButtons = document.querySelectorAll('button[type="submit"]');

                console.log('Form found:', form);
                console.log('Submit buttons found:', submitButtons.length);

                // Add click event listeners to all submit buttons
                submitButtons.forEach((button, index) => {
                    button.addEventListener('click', function(e) {
                        console.log(`Button ${index + 1} clicked:`, this);
                        console.log('Button type:', this.type);
                        console.log('Form attribute:', this.getAttribute('form'));
                    });
                });

                // Add submit event listener to form
                if (form) {
                    form.addEventListener('submit', function(e) {
                        console.log('Form is submitting!');
                        console.log('Form action:', this.action);
                        console.log('Form method:', this.method);

                        // Check if content_type is selected
                        const contentTypeInput = this.querySelector('input[name="content_type"]:checked') ||
                                                this.querySelector('input[name="content_type"][type="hidden"]');
                        console.log('Content type value:', contentTypeInput ? contentTypeInput.value : 'NOT FOUND');

                        // Uncomment to prevent submission for testing
                        // e.preventDefault();
                        // alert('Form would submit now. Check console for details.');
                    });
                }
            });
        </script>
    @endsection

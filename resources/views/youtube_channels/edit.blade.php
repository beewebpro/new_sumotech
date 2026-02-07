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

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form method="POST" action="{{ route('youtube-channels.update', $youtubeChannel) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('youtube_channels._form', ['channel' => $youtubeChannel])

                            <div class="mt-6 flex items-center gap-3">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                                    Update
                                </button>
                                <a href="{{ route('youtube-channels.index') }}"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Video') }} - {{ $youtubeChannel->title }}
            </h2>
            <a href="{{ route('youtube-channels.contents.index', $youtubeChannel) }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                Back to list
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('youtube-channels.contents.store', $youtubeChannel) }}">
                        @csrf
                        @include('youtube_channel_contents._form')

                        <div class="mt-6 flex items-center gap-3">
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                                Save
                            </button>
                            <a href="{{ route('youtube-channels.contents.index', $youtubeChannel) }}"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

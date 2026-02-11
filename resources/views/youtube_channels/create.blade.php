@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-2xl text-gray-800">
                    {{ __('Create YouTube Channel') }}
                </h2>
                <a href="{{ route('youtube-channels.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Back to list
                </a>
            </div>
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form method="POST" action="{{ route('youtube-channels.store') }}" enctype="multipart/form-data">
                            @csrf
                            @include('youtube_channels._form')

                            {{-- Create Button --}}
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div>
                                        <p class="text-sm font-semibold text-green-900">✅ Tạo kênh YouTube mới</p>
                                        <p class="text-xs text-green-700">Nhấn "Create Channel" để lưu thông tin kênh</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('youtube-channels.index') }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg transition duration-200">
                                            Cancel
                                        </a>
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 shadow-md">
                                            ✅ Create Channel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

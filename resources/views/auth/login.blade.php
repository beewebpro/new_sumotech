<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Sumotech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"
        media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css"
        media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    </noscript>
    <style>
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen bg-white/80">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center justify-between mb-2 mt-2">
                <div></div>
                <div class="relative">
                    <button id="language-button"
                        class="flex items-center gap-2 text-gray-600 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">
                        <div class="w-4 h-4 flex items-center justify-center">
                            <i class="ri-global-line"></i>
                        </div>
                        <span id="current-language">{{ strtoupper(app()->getLocale()) }}</span>
                        <div class="w-3 h-3 flex items-center justify-center">
                            <i class="ri-arrow-down-s-line text-xs"></i>
                        </div>
                    </button>
                    <div id="language-dropdown"
                        class="hidden absolute right-0 top-full mt-2 w-32 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <a href="{{ route('lang.switch', 'vi') }}"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors rounded-t-lg">Tiếng
                            Việt</a>
                        <a href="{{ route('lang.switch', 'en') }}"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors rounded-b-lg">English</a>
                    </div>
                </div>
            </div>
            <div class="flex justify-center mb-8 mt-4">
                <img src="{{ asset('img/logo/sumotech_round.png') }}" alt="Sumotech" class="h-14 w-auto" />
            </div>

            <div class="bg-white/90 backdrop-blur-sm border border-gray-100 rounded-2xl shadow-sm p-6 sm:p-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2 text-center">{{ __('login.title') }}</h1>
                <p class="text-gray-600 text-center mb-6">{{ __('login.subtitle') }}</p>

                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 text-green-700 text-sm px-4 py-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('login.email') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            autofocus autocomplete="username"
                            class="block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary shadow-sm" />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password"
                                class="block text-sm font-medium text-gray-700">{{ __('login.password') }}</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-sm text-primary hover:underline">{{ __('login.forgot') }}</a>
                            @endif
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                            class="block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary shadow-sm" />
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary">
                            <span class="ms-2 text-sm text-gray-700">{{ __('login.remember') }}</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white py-3 rounded-button font-semibold hover:bg-primary/90 transition-colors">
                        {{ __('login.submit') }}
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-gray-500 mt-6">© 2025 Sumotech</p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const languageButton = document.getElementById('language-button');
            const languageDropdown = document.getElementById('language-dropdown');
            if (languageButton && languageDropdown) {
                languageButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    languageDropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!languageButton.contains(e.target)) {
                        languageDropdown.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>

</html>

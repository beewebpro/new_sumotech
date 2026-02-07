<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Sumo Tech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }
    </style>
</head>

<body class="bg-white">
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50 border-b-2 border-red-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('dlhf_logo.png') }}" alt="Sumo Tech" class="h-12 w-auto" />
                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">ADMIN</span>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-red-600 px-3 py-2 text-sm font-medium transition-colors border-b-2 border-red-600">
                            {{ __('Admin Dashboard') }}
                        </a>
                        <a href="#"
                            class="text-gray-600 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors">
                            {{ __('User Management') }}
                        </a>
                        <a href="#"
                            class="text-gray-600 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors">
                            {{ __('System Settings') }}
                        </a>
                        <a href="#"
                            class="text-gray-600 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors">
                            {{ __('Reports') }}
                        </a>
                        <div class="relative">
                            <button id="user-menu-button"
                                class="flex items-center gap-2 text-gray-600 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors">
                                <div class="w-4 h-4 flex items-center justify-center">
                                    <i class="ri-user-line"></i>
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                                <div class="w-3 h-3 flex items-center justify-center">
                                    <i class="ri-arrow-down-s-line text-xs"></i>
                                </div>
                            </button>
                            <div id="user-dropdown"
                                class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <a href="{{ route('profile.edit') }}"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors rounded-t-lg">
                                    {{ __('Profile') }}
                                </a>
                                <a href="{{ route('dashboard') }}"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors">
                                    {{ __('User Dashboard') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors rounded-b-lg">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-600 hover:text-red-600 p-2">
                        <div class="w-6 h-6 flex items-center justify-center">
                            <i class="ri-menu-line ri-lg"></i>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="text-red-600 block px-3 py-2 text-base font-medium">{{ __('Admin Dashboard') }}</a>
                <a href="#" class="text-gray-600 hover:text-red-600 block px-3 py-2 text-base font-medium">
                    {{ __('User Management') }}
                </a>
                <a href="#" class="text-gray-600 hover:text-red-600 block px-3 py-2 text-base font-medium">
                    {{ __('System Settings') }}
                </a>
                <a href="#" class="text-gray-600 hover:text-red-600 block px-3 py-2 text-base font-medium">
                    {{ __('Reports') }}
                </a>
                <div class="border-t border-gray-200 mt-4 pt-4">
                    <div class="px-3 py-2">
                        <p class="text-sm font-medium text-gray-900 mb-2">{{ Auth::user()->name }}</p>
                        <div class="space-y-1">
                            <a href="{{ route('profile.edit') }}"
                                class="block w-full text-left px-3 py-2 text-sm text-gray-600 hover:text-red-600 hover:bg-gray-50 rounded-md transition-colors">
                                {{ __('Profile') }}
                            </a>
                            <a href="{{ route('dashboard') }}"
                                class="block w-full text-left px-3 py-2 text-sm text-gray-600 hover:text-red-600 hover:bg-gray-50 rounded-md transition-colors">
                                {{ __('User Dashboard') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-3 py-2 text-sm text-gray-600 hover:text-red-600 hover:bg-gray-50 rounded-md transition-colors">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <section class="pt-16 min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="w-full">
                <div class="max-w-3xl mb-12">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-16 h-16 bg-red-500 rounded-xl flex items-center justify-center">
                            <i class="ri-shield-star-line ri-2x text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                                {{ __('Admin Panel') }}
                            </h1>
                            <p class="text-lg text-gray-600">
                                {{ __('Welcome') }}, <span
                                    class="font-semibold text-red-600">{{ Auth::user()->name }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <p class="text-sm text-red-800">
                            <i class="ri-information-line"></i>
                            {{ __('You are logged in as Administrator. You have full access to system management features.') }}
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <div
                        class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow border-l-4 border-red-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                <i class="ri-team-line ri-xl text-red-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">{{ __('Total') }}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">0</h3>
                        <p class="text-gray-600">{{ __('Total Users') }}</p>
                    </div>

                    <div
                        class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow border-l-4 border-orange-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                <i class="ri-store-line ri-xl text-orange-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">{{ __('Active') }}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">0</h3>
                        <p class="text-gray-600">{{ __('Retail Stores') }}</p>
                    </div>

                    <div
                        class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow border-l-4 border-green-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="ri-line-chart-line ri-xl text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">{{ __('This Month') }}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">0 VNĐ</h3>
                        <p class="text-gray-600">{{ __('Total Revenue') }}</p>
                    </div>

                    <div
                        class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow border-l-4 border-blue-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="ri-database-line ri-xl text-blue-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">{{ __('System') }}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">0</h3>
                        <p class="text-gray-600">{{ __('Total Records') }}</p>
                    </div>
                </div>

                <!-- Admin Quick Actions -->
                <div class="bg-white rounded-2xl p-8 shadow-sm mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <i class="ri-tools-line text-red-600"></i>
                        {{ __('Admin Tools') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="#"
                            class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-red-500 hover:bg-red-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center group-hover:bg-red-500 transition-colors">
                                <i class="ri-user-add-line text-red-600 group-hover:text-white"></i>
                            </div>
                            <span class="font-medium text-gray-700">{{ __('Add User') }}</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-orange-500 hover:bg-orange-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-500 transition-colors">
                                <i class="ri-store-add-line text-orange-600 group-hover:text-white"></i>
                            </div>
                            <span class="font-medium text-gray-700">{{ __('Add Store') }}</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                                <i class="ri-file-chart-line text-blue-600 group-hover:text-white"></i>
                            </div>
                            <span class="font-medium text-gray-700">{{ __('View All Reports') }}</span>
                        </a>
                        <a href="#"
                            class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition-all group">
                            <div
                                class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-500 transition-colors">
                                <i class="ri-settings-3-line text-purple-600 group-hover:text-white"></i>
                            </div>
                            <span class="font-medium text-gray-700">{{ __('System Config') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <i class="ri-time-line text-red-600"></i>
                        {{ __('Recent System Activity') }}
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="ri-check-line text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ __('System Status') }}</p>
                                <p class="text-xs text-gray-500">{{ __('All systems operational') }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ __('Just now') }}</span>
                        </div>
                        <div class="text-center py-8 text-gray-500">
                            <i class="ri-inbox-line ri-3x mb-2"></i>
                            <p>{{ __('No recent activity to display') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('user-menu-button');
            const userDropdown = document.getElementById('user-dropdown');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (userMenuButton && userDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function(e) {
                    if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                        userDropdown.classList.add('hidden');
                    }
                });
            }

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Giftbin'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <span class="text-2xl font-bold text-indigo-600">🎁 Giftbin</span>
                        </a>
                    </div>

                    <div class="flex items-center space-x-8">
                        <!-- Navigation Links -->
                        <div class="hidden md:flex items-center space-x-6">
                            <a href="{{ route('home') }}" class="text-sm text-gray-700 hover:text-gray-900 {{ request()->routeIs('home') ? 'font-semibold' : '' }}">Home</a>
                            <a href="{{ route('events') }}" class="text-sm text-gray-700 hover:text-gray-900 {{ request()->routeIs('events') ? 'font-semibold' : '' }}">Events</a>
                        </div>

                        <!-- User Menu -->
                        <div class="flex items-center space-x-4">
                            @auth
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <div>
                                        <button @click="open = !open" type="button" class="inline-flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                            <span>{{ auth()->user()->name }}</span>
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                        <div class="py-1" role="none">
                                            <a href="{{ route('account') }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Account Settings</a>
                                            @can('access admin panel')
                                                <a href="/admin" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">Admin Panel</a>
                                            @endcan
                                            <hr class="border-gray-200">
                                            <form method="POST" action="{{ route('logout') }}" class="block">
                                                @csrf
                                                <button type="submit" class="text-red-600 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100" role="menuitem">
                                                    Logout
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Login</a>
                                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                                    Sign Up
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500">
                    © {{ date('Y') }} Giftbin. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
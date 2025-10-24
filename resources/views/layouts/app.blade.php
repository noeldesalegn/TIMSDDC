<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            @php
                                $user = auth()->user();
                                $dashboardRoute = $user ? match($user->role) {
                                    'admin' => route('admin.dashboard'),
                                    'taxpayer' => route('taxpayer.dashboard'),
                                    'interviewer' => route('interviewer.dashboard'),
                                    default => route('dashboard'),
                                } : url('/');
                            @endphp
                            <a href="{{ $dashboardRoute }}" class="flex items-center gap-2">
                                <x-application-logo class="h-8 w-8 text-blue-600" />
                                <span class="hidden sm:block text-sm sm:text-lg md:text-xl font-semibold text-gray-900 dark:text-gray-100 leading-tight">Dire Dawa City Tax Information Management System</span>
                            </a>
                        </div>

                        <div class="flex items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                        <div class="text-left">
                                            <div>{{ Auth::user()->name }}</div>
                                            <div class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</div>
                                        </div>
                                        <div class="ms-2">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex">
                <aside :class="{'-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen}" class="fixed sm:static inset-y-0 left-0 z-40 w-64 transform bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-transform duration-200 ease-in-out sm:translate-x-0">
                    <nav class="p-4 space-y-2">
                        @php $role = Auth::user()->role ?? null; @endphp
                        @if ($role === 'admin')
                            @include('layouts.partials.nav-admin')
                        @elseif ($role === 'taxpayer')
                            @include('layouts.partials.nav-taxpayer')
                        @elseif ($role === 'interviewer')
                            @include('layouts.partials.nav-interviewer')
                        @else
                            <div class="text-sm text-gray-500">No navigation available.</div>
                        @endif
                    </nav>
                </aside>
                <div @click="sidebarOpen = false" :class="{'block': sidebarOpen, 'hidden': !sidebarOpen}" class="fixed inset-0 z-30 bg-black/50 sm:hidden"></div>

                <div class="flex-1">
                    @isset($header)
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="p-4 sm:p-6 lg:p-8">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>

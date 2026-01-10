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
    <body class="font-sans text-slate-100 antialiased bg-slate-950 min-h-screen flex flex-col items-center sm:justify-center relative">
        <!-- Background decorations -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -left-32 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 h-[28rem] w-[28rem] rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-sky-500/40 via-slate-900/0 to-transparent"></div>
        </div>

        <div class="relative w-full sm:max-w-md px-6 flex flex-col items-center py-12">
            <div class="mb-6">
                <a href="/">
                    <x-application-logo class="w-24 h-24" />
                </a>
            </div>

            <div class="w-full px-6 py-6 bg-slate-900/70 backdrop-blur-md border border-white/5 shadow-xl shadow-slate-950/40 overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

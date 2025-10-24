<x-app-layout>
    @hasSection('header')
        <x-slot name="header">
            @yield('header')
        </x-slot>
    @else
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Taxpayer</h2>
        </x-slot>
    @endif

    @hasSection('content')
        @yield('content')
    @else
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                Welcome to the Taxpayer area. Replace this with real content.
            </div>
        </div>
    @endif
</x-app-layout>

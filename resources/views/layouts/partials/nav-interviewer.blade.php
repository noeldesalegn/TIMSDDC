@php
    $linkBase = 'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors';
    $linkIdle = 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700';
    $linkActive = 'bg-indigo-50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-600';
    $isDashboard = request()->routeIs('interviewer.dashboard');
@endphp
<nav class="space-y-1">
    <a href="{{ route('interviewer.dashboard') }}" class="{{ $linkBase }} {{ $isDashboard ? $linkActive : $linkIdle }}">
        <span>Dashboard</span>
    </a>
    <a href="#" class="{{ $linkBase }} {{ $linkIdle }}">
        <span>Upload Files</span>
    </a>
    <a href="#" class="{{ $linkBase }} {{ $linkIdle }}">
        <span>My Schedule</span>
    </a>
    <a href="#" class="{{ $linkBase }} {{ $linkIdle }}">
        <span>Generate Reports</span>
    </a>
</nav>

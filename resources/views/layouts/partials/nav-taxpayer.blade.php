@php
    $linkBase = 'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors';
    $linkIdle = 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700';
    $linkActive = 'bg-indigo-50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-600';
    $isDashboard = request()->routeIs('taxpayer.dashboard');
    $isSummary = request()->routeIs('taxpayer.summary');
    $isPayment = request()->routeIs('taxpayer.payment*');
    $isComplaints = request()->routeIs('taxpayer.complaints*');
    $isNews = request()->routeIs('taxpayer.news*');
@endphp
<nav class="space-y-1">
    <a href="{{ route('taxpayer.dashboard') }}" class="{{ $linkBase }} {{ $isDashboard ? $linkActive : $linkIdle }}">
        <span>Dashboard</span>
    </a>
    <a href="{{ route('taxpayer.summary') }}" class="{{ $linkBase }} {{ $isSummary ? $linkActive : $linkIdle }}">
        <span>Tax Summary</span>
    </a>
    <a href="{{ route('taxpayer.payment') }}" class="{{ $linkBase }} {{ $isPayment ? $linkActive : $linkIdle }}">
        <span>Make Payment</span>
    </a>
    <a href="{{ route('taxpayer.complaints') }}" class="{{ $linkBase }} {{ $isComplaints ? $linkActive : $linkIdle }}">
        <span>Send Complaint</span>
    </a>
    <a href="{{ route('taxpayer.news') }}" class="{{ $linkBase }} {{ $isNews ? $linkActive : $linkIdle }}">
        <span>News & Updates</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="{{ $linkBase }} {{ $linkIdle }}">
        <span>My Profile</span>
    </a>
</nav>

@php
    $linkBase = 'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors';
    $linkIdle = 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700';
    $linkActive = 'bg-indigo-50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-600';
    $isDashboard = request()->routeIs('admin.dashboard');
    $isTaxpayers = request()->routeIs('admin.taxpayers.index');
    $isIninterviewers = request()->routeIs('admin.interviewers.index');
    $isTaxpayerPayments = request()->routeIs('admin.taxpayers.payments');
    $isNews = request()->routeIs('admin.news.*');
    $isReports = request()->routeIs('admin.reports.*');
    $isComplaints = request()->routeIs('admin.complaints.*');
    $isAdminUsers = request()->routeIs('admin.users.*');
@endphp
<nav class="space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="{{ $linkBase }} {{ $isDashboard ? $linkActive : $linkIdle }}" @if($isDashboard) aria-current="page" @endif>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.taxpayers.index') }}" class="{{ $linkBase }} {{ $isTaxpayers ? $linkActive : $linkIdle }}" @if($isTaxpayers) aria-current="page" @endif>
        <span>Manage Taxpayers</span>
    </a>
    <a href="{{ route('admin.taxpayers.payments') }}"
       class="{{ $linkBase }} {{ $isTaxpayerPayments ? $linkActive : $linkIdle }}">
        <span>Taxpayer Payments</span>
    </a>
    <a href="{{ route('admin.interviewers.index') }}" class="{{ $linkBase }} {{ $isIninterviewers ? $linkActive : $linkIdle }}" @if($isIninterviewers) aria-current="page" @endif>
        <span>Manage Interviewers</span>
    </a>
    <a href="{{ route('admin.news.index') }}" class="{{ $linkBase }} {{ $isNews ? $linkActive : $linkIdle }}" @if($isNews) aria-current="page" @endif>
        <span>Post News</span>
    </a>
    <a href="{{ route('admin.tax.index') }}" class="{{ $linkBase }} {{ $linkIdle }}">
        <span>Tax Calculation</span>
    </a>
    <a href="{{ route('admin.reports.index') }}" class="{{ $linkBase }} {{ $isReports ? $linkActive : $linkIdle }}" @if($isReports) aria-current="page" @endif>
        <span>Generate Reports</span>
    </a>
    <a href="{{ route('admin.complaints.index') }}" class="{{ $linkBase }} {{ $isComplaints ? $linkActive : $linkIdle }}" @if($isComplaints) aria-current="page" @endif>
        <span>Complaints & Comments</span>
    </a>
    <a href="{{ route('admin.users.create') }}" class="{{ $linkBase }} {{ $isAdminUsers ? $linkActive : $linkIdle }}" @if($isAdminUsers) aria-current="page" @endif>
        <span>Create user</span>
    </a>
</nav>

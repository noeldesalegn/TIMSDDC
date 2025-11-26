@php
    $linkBase = 'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors';
    $linkIdle = 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700';
    $linkActive = 'bg-indigo-50 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-600';
    $isDashboard = request()->routeIs('cashier.dashboard');
    $isPayments = request()->routeIs('cashier.payments.index') || request()->routeIs('cashier.payments.receipt') || request()->routeIs('cashier.payments.refund');
    $isNewPayment = request()->routeIs('cashier.payments.create');
@endphp
<nav class="space-y-1">
    <a href="{{ route('cashier.dashboard') }}" class="{{ $linkBase }} {{ $isDashboard ? $linkActive : $linkIdle }}">
        <span>Dashboard</span>
    </a>
    <a href="{{ route('cashier.payments.index') }}" class="{{ $linkBase }} {{ $isPayments ? $linkActive : $linkIdle }}">
        <span>Payments</span>
    </a>
    <a href="{{ route('cashier.payments.create') }}" class="{{ $linkBase }} {{ $isNewPayment ? $linkActive : $linkIdle }}">
        <span>New Payment</span>
    </a>
</nav>

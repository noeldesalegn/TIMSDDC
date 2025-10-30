<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Top Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow">
                    <p class="text-sm opacity-80 mb-2">Total Taxpayers</p>
                    <p class="text-3xl font-bold">{{ $totalTaxpayers }}</p>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow">
                    <p class="text-sm opacity-80 mb-2">Total Revenue</p>
                    <p class="text-3xl font-bold">ETB {{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-lg shadow">
                    <p class="text-sm opacity-80 mb-2">Pending Payments</p>
                    <p class="text-3xl font-bold">{{ $pendingPayments }}</p>
                </div>
            </div>

            <!-- Tax Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="font-semibold mb-4 text-gray-800 dark:text-gray-200">Tax Overview</h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-2">Total Tax Paid: <span class="font-semibold text-green-600">ETB {{ number_format($totalTaxPaid, 2) }}</span></p>
                    <p class="text-gray-700 dark:text-gray-300">Tax Due: <span class="font-semibold text-red-600">ETB {{ number_format($totalTaxDue, 2) }}</span></p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="font-semibold mb-4 text-gray-800 dark:text-gray-200">Complaints Overview</h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-2">Total Complaints: {{ $totalComplaints }}</p>
                    <p class="text-gray-700 dark:text-gray-300">Unresolved: <span class="text-red-500 font-semibold">{{ $unresolvedComplaints }}</span></p>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recent Payments -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="font-semibold mb-4 text-gray-800 dark:text-gray-200">Recent Payments</h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($recentPayments as $payment)
                            <li class="py-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-900 dark:text-gray-100">{{ $payment->user->name }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->created_at->format('M d, Y') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">ETB {{ number_format($payment->amount, 2) }} - <span class="capitalize">{{ $payment->status }}</span></p>
                            </li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">No recent payments</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Recent Complaints -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="font-semibold mb-4 text-gray-800 dark:text-gray-200">Recent Complaints</h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($recentComplaintsList as $complaint)
                            <li class="py-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-900 dark:text-gray-100">{{ $complaint->user->name }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $complaint->created_at->format('M d, Y') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $complaint->subject }}</p>
                            </li>
                        @empty
                            <li class="text-gray-500 dark:text-gray-400">No complaints found</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

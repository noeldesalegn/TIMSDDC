<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Taxpayer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tax Summary Overview Card -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-700 dark:to-blue-800 overflow-hidden shadow-lg sm:rounded-lg text-white">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Tax Summary Overview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm opacity-90 mb-1">Current Balance Due</p>
                                <p class="text-3xl font-bold">ETB {{ number_format($taxSummary['total_tax'], 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-90 mb-1">Due Date</p>
                                <p class="text-2xl font-semibold">{{ \Carbon\Carbon::parse($taxSummary['due_date'])->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <a href="{{ route('taxpayer.payment') }}" class="flex flex-col items-center justify-center p-6 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition border-2 border-blue-200 dark:border-blue-800">
                                <div class="p-3 bg-blue-600 dark:bg-blue-700 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Make Payment</p>
                            </a>
                            <a href="{{ route('taxpayer.summary') }}" class="flex flex-col items-center justify-center p-6 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition border-2 border-green-200 dark:border-green-800">
                                <div class="p-3 bg-green-600 dark:bg-green-700 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">View Tax Summary</p>
                            </a>
                            <a href="{{ route('taxpayer.complaints') }}" class="flex flex-col items-center justify-center p-6 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition border-2 border-red-200 dark:border-red-800">
                                <div class="p-3 bg-red-600 dark:bg-red-700 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                    </svg>
                                </div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Send Complaint</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Payment History Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment History</h3>
                        @if(count($paymentHistory) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($paymentHistory as $payment)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($payment['date'])->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">ETB {{ number_format($payment['amount'], 2) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $payment['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>No payment history available</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Recent News -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent News</h3>
                        <div class="space-y-4">
                            @foreach($recentNews as $news)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $news['title'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $news['excerpt'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">{{ \Carbon\Carbon::parse($news['date'])->format('M d, Y') }}</p>
                            </div>
                            @endforeach
                            <div class="mt-4">
                                <a href="{{ route('taxpayer.news') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                                    View All News →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

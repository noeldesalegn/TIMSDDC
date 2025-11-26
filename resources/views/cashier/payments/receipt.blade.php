<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Payment Receipt
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4 text-sm text-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-green-200 dark:border-green-700">
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Official Payment Receipt</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Reference: {{ $receipt['reference'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-900 dark:text-gray-100">
                        <div class="space-y-2">
                            <p><span class="font-semibold">Taxpayer Name:</span> {{ $receipt['taxpayer']['name'] ?? 'N/A' }}</p>
                            <p><span class="font-semibold">Email:</span> {{ $receipt['taxpayer']['email'] ?? 'N/A' }}</p>
                            <p><span class="font-semibold">TIN:</span> {{ $receipt['tin'] ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-2">
                            <p><span class="font-semibold">Amount Paid:</span> ETB {{ number_format($receipt['amount'], 2) }}</p>
                            <p><span class="font-semibold">Payment Method:</span> {{ ucfirst($receipt['payment_method']) }}</p>
                            <p><span class="font-semibold">Payment Date:</span> {{ $receipt['created_at'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-900 dark:text-gray-100">
                        <div>
                            <p class="font-semibold mb-1">Processed By</p>
                            <p>{{ $receipt['processed_by']['name'] ?? 'N/A' }}</p>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 md:text-right">
                            <p>This is a system-generated receipt. No signature is required.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md">
                            Print Receipt
                        </button>
                        <a href="{{ route('cashier.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-semibold rounded-md">
                            Back to Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

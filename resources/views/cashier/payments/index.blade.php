<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cashier Payments
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filter Payments</h3>
                        <a href="{{ route('cashier.payments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                            New Payment
                        </a>
                    </div>

                    <form method="GET" action="{{ route('cashier.payments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">TIN</label>
                            <input type="text" name="tin" value="{{ $filters['tin'] ?? '' }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="refunded" {{ ($filters['status'] ?? '') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
                            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
                            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="only_mine" value="1" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 focus:ring-indigo-500" {{ $onlyMine ? 'checked' : '' }}>
                                <span class="ml-2">Only my payments</span>
                            </label>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Per page</label>
                            <select name="per_page" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach([10,15,25,50,100] as $size)
                                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-5 flex justify-end gap-3">
                            <a href="{{ route('cashier.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-medium rounded-md">
                                Reset
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" id="payment-history"
                 hx-get="{{ request()->fullUrl() }}"
                 hx-trigger="every 15s"
                 hx-select="#payment-history"
                 hx-target="#payment-history"
                 hx-swap="outerHTML">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payment History</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Showing {{ $payments->firstItem() }}-{{ $payments->lastItem() }} of {{ $payments->total() }} payments</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Date</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Taxpayer</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">TIN</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Amount</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Status</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Processed By</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Receipt</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($payments as $payment)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ optional($payment->created_at)->format('M d, Y H:i') }}</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ optional($payment->user)->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $payment->tin ?? optional($payment->user)->tin ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">ETB {{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($payment->status === 'paid') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                                @elseif($payment->status === 'refunded') bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100
                                                @elseif($payment->status === 'pending') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @elseif($payment->status === 'rejected') bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200
                                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ optional($payment->processedBy)->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                            @if ($payment->receipt_path)
                                                @if (Str::endsWith($payment->receipt_path, ['.jpg', '.jpeg', '.png']))
                                                    <a href="{{ asset('storage/'.$payment->receipt_path) }}" target="_blank">
                                                        <img src="{{ asset('storage/'.$payment->receipt_path) }}" alt="Receipt" class="h-12 rounded">
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/'.$payment->receipt_path) }}" target="_blank" class="text-indigo-600 underline">View PDF</a>
                                                @endif
                                            @else
                                                <span class="text-gray-500 text-sm">No receipt</span>
                                            @endif
                                        <td class="px-4 py-2 text-right space-x-2">
                                            <a href="{{ route('cashier.payments.receipt', $payment) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-md">Receipt</a>
                                            @if($payment->status === 'completed')
                                                <form method="POST" action="{{ route('cashier.payments.refund', $payment) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center mt-2 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md" onclick="return confirm('Refund this payment?')">
                                                        Refund
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No payments found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

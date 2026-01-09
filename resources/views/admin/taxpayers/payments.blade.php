<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Payment Receipts</h2>
    </x-slot>

    <div class="py-6" id="payments-table"
         hx-get="{{ request()->fullUrl() }}"
         hx-trigger="every 15s"
         hx-select="#payments-table"
         hx-target="#payments-table"
         hx-swap="outerHTML">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
            @elseif (session('error'))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">User</th>
                            <th class="px-4 py-2 text-left">TIN</th>
                            <th class="px-4 py-2 text-left">Amount</th>
                            <th class="px-4 py-2 text-left">Bank</th>
                            <th class="px-4 py-2 text-left">Receipt</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($payments as $payment)
                            <tr class="border-b dark:border-gray-700 text-gray-900 dark:text-gray-100">
                                <td class="px-4 py-2">{{ $payment->user->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $payment->user->tin ?? 'N/A' }}</td>
                                <td class="px-4 py-2">ETB {{ number_format($payment->amount, 2) }}</td>
                                <td class="px-4 py-2">{{ $payment->bank_name }}</td>
                                <td class="px-4 py-2">
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
                                </td>
                                <td class="px-4 py-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $payment->verification_status === 'verified' ? 'bg-green-100 text-green-800' :
                                               ($payment->verification_status === 'rejected' ? 'bg-red-100 text-red-800' :
                                               'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($payment->verification_status) }}
                                        </span>
                                </td>
                                <td class="px-4 py-2 flex space-x-2">
                                    @if ($payment->verification_status === 'pending')
                                        <form method="POST" action="{{ route('admin.taxpayers.payments.verify', $payment->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">Verify</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.taxpayers.payments.reject', $payment->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-gray-500 text-sm">Reviewed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

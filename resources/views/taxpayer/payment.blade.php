<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Make Payment</h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 border border-green-200 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Amount Due</div>
                    <div class="text-2xl font-bold text-indigo-600">{{ number_format($amountDue, 2) }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Due Date</div>
                    <div class="text-lg font-medium">{{ $dueDate }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('taxpayer.payment.process') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">TIN</label>
                    <input name="tin" value="{{ old('tin') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required />
                    @error('tin') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Bank Name</label>
                    <input name="bank_name" value="{{ old('bank_name') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required />
                    @error('bank_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Account Number</label>
                    <input name="account_number" value="{{ old('account_number') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required />
                    @error('account_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Amount</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $amountDue) }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required />
                    @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-700 disabled:opacity-25 transition">
                        Pay Now
                    </button>
                </div>
            </form>

            @if (session('payment_receipt'))
                @php $r = session('payment_receipt'); @endphp
                <div class="border rounded-md p-4">
                    <div class="font-semibold mb-2">Payment Receipt (Placeholder)</div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        <div><dt class="text-gray-500">Reference</dt><dd class="font-medium">{{ $r['reference'] }}</dd></div>
                        <div><dt class="text-gray-500">Paid At</dt><dd class="font-medium">{{ $r['paid_at'] }}</dd></div>
                        <div><dt class="text-gray-500">TIN</dt><dd class="font-medium">{{ $r['tin'] }}</dd></div>
                        <div><dt class="text-gray-500">Bank</dt><dd class="font-medium">{{ $r['bank_name'] }}</dd></div>
                        <div><dt class="text-gray-500">Account</dt><dd class="font-medium">{{ $r['account_number'] }}</dd></div>
                        <div><dt class="text-gray-500">Amount</dt><dd class="font-medium">{{ number_format($r['amount'], 2) }}</dd></div>
                    </dl>
                    <div class="mt-3">
                        <button class="inline-flex items-center px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-md text-sm" disabled>
                            Download Receipt (coming soon)
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Make Payment</h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Success Message --}}
        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">Payment Successful!</h3>
                        <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Payment Summary Card --}}
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 dark:from-indigo-700 dark:to-purple-800 overflow-hidden shadow-lg sm:rounded-lg text-white">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Payment Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm opacity-90 mb-2">Amount Due</p>
                        <p class="text-4xl font-bold">ETB {{ number_format($amountDue, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm opacity-90 mb-2">Due Date</p>
                        <p class="text-2xl font-semibold">{{ \Carbon\Carbon::parse($dueDate)->format('M d, Y') }}</p>
                        <p class="text-sm mt-2 opacity-75">{{ \Carbon\Carbon::parse($dueDate)->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Form --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Enter Payment Information</h3>

                <form method="POST" action="{{ route('taxpayer.payment.process') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- TIN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tax Identification Number (TIN)</label>
                        <input
                            name="tin"
                            value="{{ auth()->user()->tin ?? old('tin') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter your TIN"
                            required
                        />
                        @error('tin')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bank Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bank Name</label>
                        <input
                            name="bank_name"
                            value="{{ old('bank_name') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter bank name"
                            required
                        />
                        @error('bank_name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Account Number --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Number</label>
                        <input
                            name="account_number"
                            value="{{ old('account_number') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter account number"
                            required
                        />
                        @error('account_number')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Amount (ETB)</label>
                        <input
                            type="number"
                            step="0.01"
                            name="amount"
                            value="{{ old('amount', $amountDue) }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                            required
                        />
                        @error('amount')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="card">Card</option>
                        </select>
                    </div>

                    {{-- Receipt Photo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload Payment Receipt (optional)</label>
                        <input
                            type="file"
                            name="receipt_photo"
                            accept="image/*,application/pdf"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        @error('receipt_photo')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Submit Payment
                        </button>
                        <a href="{{ route('taxpayer.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Payment Receipt --}}
        @if (session('receipt_path'))
            @php $receipt = session('receipt_path'); @endphp
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-2 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Payment Receipt</h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Paid
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 text-gray-900 dark:text-gray-100">
                        <div class="space-y-3">
                            <p><strong>Transaction Reference:</strong> {{ $receipt['reference'] }}</p>
                            <p><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($receipt['paid_at'])->format('M d, Y h:i A') }}</p>
                            <p><strong>Amount Paid:</strong> ETB {{ number_format($receipt['amount'], 2) }}</p>
                        </div>
                        <div class="space-y-3">
                            <p><strong>TIN:</strong> {{ $receipt['tin'] }}</p>
                            <p><strong>Bank:</strong> {{ $receipt['bank_name'] }}</p>
                            <p><strong>Account Number:</strong> {{ $receipt['account_number'] }}</p>
                        </div>
                    </div>
                    @if (!empty($receipt['receipt_path']))
                        <div class="mt-4 border-t pt-4">
                            <p class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Uploaded Receipt:</p>
                            <a href="{{ Storage::url($receipt['receipt_path']) }}"
                               target="_blank"
                               class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 underline">
                                View Receipt
                            </a>
                        </div>
                    @endif

                    <div class="flex gap-4">
                        <button onclick="window.print()" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            Print Receipt
                        </button>
                        <a href="{{ route('taxpayer.dashboard') }}" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

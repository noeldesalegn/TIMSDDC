<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Make Payment</h2>
    </x-slot>

    <div x-data="{ showConfirmation: false, paymentMethod: 'bank_transfer' }" class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">Payment Successful!</h3>
                        <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payment Amount Card -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 dark:from-indigo-700 dark:to-purple-800 overflow-hidden shadow-lg sm:rounded-lg text-white">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-6">Payment Summary</h3>
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

        <!-- Payment Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" x-show="!showConfirmation">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Payment Information</h3>
                
                <form method="POST" action="{{ route('taxpayer.payment.process') }}" class="space-y-6" @submit.prevent="showConfirmation = true">
                    @csrf
                    
                    <!-- TIN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tax Identification Number (TIN)
                        </label>
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

                    <!-- Bank Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Bank Name
                        </label>
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

                    <!-- Account Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Account Number
                        </label>
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

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Payment Amount (ETB)
                        </label>
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
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">You can pay any amount up to the total due</p>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Payment Method
                        </label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-indigo-500 transition" :class="paymentMethod === 'bank_transfer' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-700'">
                                <input type="radio" x-model="paymentMethod" value="bank_transfer" class="sr-only">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Bank Transfer</p>
                                </div>
                            </label>
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-indigo-500 transition" :class="paymentMethod === 'mobile_banking' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-700'">
                                <input type="radio" x-model="paymentMethod" value="mobile_banking" class="sr-only">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Mobile Banking</p>
                                </div>
                            </label>
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-indigo-500 transition" :class="paymentMethod === 'card' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-700'">
                                <input type="radio" x-model="paymentMethod" value="card" class="sr-only">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">Card</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Proceed to Confirmation
                        </button>
                        <a href="{{ route('taxpayer.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div x-show="showConfirmation" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.away="showConfirmation = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Confirm Payment</h3>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Amount</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">ETB {{ number_format($amountDue, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Payment Method</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize" x-text="paymentMethod.replace('_', ' ')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Tax Identification Number</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="'[Entered in form]'"></span>
                    </div>
                </div>

                <form method="POST" action="{{ route('taxpayer.payment.process') }}" id="paymentForm">
                    @csrf
                    <!-- Hidden inputs will be populated by JS -->
                </form>

                <div class="flex gap-4">
                    <button type="button" @click="showConfirmation = false" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">
                        Cancel
                    </button>
                    <button type="button" onclick="document.getElementById('paymentForm').submit()" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Confirm & Pay
                    </button>
                </div>
            </div>
        </div>

        <!-- Payment Receipt -->
        @if (session('payment_receipt'))
            @php $receipt = session('payment_receipt'); @endphp
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-2 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Payment Receipt</h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Paid
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-3">
                            <div>
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Transaction Reference</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $receipt['reference'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Payment Date</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($receipt['paid_at'])->format('M d, Y h:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Amount Paid</dt>
                                <dd class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">ETB {{ number_format($receipt['amount'], 2) }}</dd>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <dt class="text-sm text-gray-600 dark:text-gray-400">TIN</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $receipt['tin'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Bank</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $receipt['bank_name'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Account Number</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $receipt['account_number'] }}</dd>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button onclick="window.print()" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
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

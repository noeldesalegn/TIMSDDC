<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cashier Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white p-6 rounded-lg shadow">
                    <p class="text-sm opacity-90 mb-2">Total Processed Today</p>
                    <p class="text-3xl font-bold">ETB {{ number_format($totalProcessedToday, 2) }}</p>
                </div>
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white p-6 rounded-lg shadow">
                    <p class="text-sm opacity-90 mb-2">Payments Processed</p>
                    <p class="text-3xl font-bold">{{ $myProcessedCount }}</p>
                </div>
                <div class="bg-gradient-to-r from-slate-600 to-slate-700 text-white p-6 rounded-lg shadow flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90 mb-2">Cashier ID</p>
                        <p class="text-2xl font-semibold">{{ $cashierId }}</p>
                    </div>
                    <a href="{{ route('cashier.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 rounded-md text-sm font-medium">
                        View Payments
                    </a>
                </div>
            </div>

            <div x-data="{
                    tin: '',
                    loading: false,
                    error: '',
                    result: null,
                    lookup() {
                        if (!this.tin) {
                            this.error = 'TIN is required';
                            this.result = null;
                            return;
                        }
                        this.loading = true;
                        this.error = '';
                        this.result = null;
                        fetch('{{ route('cashier.taxpayers.verify') }}?tin=' + encodeURIComponent(this.tin), {
                            headers: { 'Accept': 'application/json' }
                        }).then(async (response) => {
                            const data = await response.json().catch(() => ({}));
                            this.loading = false;
                            if (!response.ok) {
                                this.error = data.error || 'Lookup failed';
                                return;
                            }
                            this.result = data;
                        }).catch(() => {
                            this.loading = false;
                            this.error = 'Network error';
                        });
                    }
                }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Taxpayer Lookup</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Search taxpayer by TIN to verify identity and outstanding tax.</p>
                        </div>
                        <a href="{{ route('cashier.payments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md">
                            New Payment
                        </a>
                    </div>

                    <form @submit.prevent="lookup" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Identification Number (TIN)</label>
                            <input x-model="tin" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter TIN">
                        </div>
                        <div class="flex gap-3 md:justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md disabled:opacity-50" :disabled="loading">
                                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span>Verify Taxpayer</span>
                            </button>
                            <a :href="tin ? ('{{ route('cashier.payments.create') }}?tin=' + encodeURIComponent(tin)) : '{{ route('cashier.payments.create') }}'" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-semibold rounded-md">
                                Payment Form
                            </a>
                        </div>
                    </form>

                    <p x-show="error" x-text="error" class="text-sm text-red-600 dark:text-red-400"></p>

                    <div x-show="result" x-cloak class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Taxpayer</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="result.taxpayer.name"></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="result.taxpayer.email"></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">TIN: <span x-text="result.taxpayer.tin"></span></p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Account</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Account No: <span x-text="result.account.account_number || 'N/A'"></span></p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Balance: ETB <span x-text="Number(result.account.balance).toFixed(2)"></span></p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Outstanding Tax</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="result.due_amount !== null ? 'ETB ' + Number(result.due_amount).toFixed(2) : 'N/A'"></p>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Recent Payments</p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Date</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Amount</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Status</th>
                                            <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-300">Processed By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" x-show="result.recent_payments && result.recent_payments.length">
                                        <template x-for="payment in result.recent_payments" :key="payment.id">
                                            <tr>
                                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100" x-text="payment.created_at"></td>
                                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100" x-text="'ETB ' + Number(payment.amount).toFixed(2)"></td>
                                                <td class="px-3 py-2">
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                          :class="payment.status === 'completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'">
                                                        <span x-text="payment.status"></span>
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">
                                                    <span x-text="payment.processed_by ? payment.processed_by.name : 'N/A'"></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tbody x-show="!result.recent_payments || result.recent_payments.length === 0">
                                        <tr>
                                            <td colspan="4" class="px-3 py-4 text-center text-gray-500 dark:text-gray-400">No payments found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

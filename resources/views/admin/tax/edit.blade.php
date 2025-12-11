<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Calculate Tax for {{ $summary->taxpayer->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Error Panel --}}
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                    <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Success Message --}}
            @if (session('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4 text-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">

                    {{-- TITLE --}}
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Tax Details
                    </h3>

                    {{-- Preview Card --}}
                    <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-4 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2">
                            Current Summary Preview
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm dark:text-white ">
                            <p><strong>Taxpayer:</strong> {{ $summary->taxpayer->name }}</p>
                            <p><strong>Tax Type:</strong> {{ $summary->tax_type }}</p>
                            <p><strong>Taxable Income:</strong> {{ number_format($summary->taxable_income, 2) }} ETB</p>
                            <p><strong>Tax Rate:</strong> {{ $summary->tax_rate }}%</p>
                            <p><strong>Deductible:</strong> {{ number_format($summary->deductible, 2) }} ETB</p>

                            <p><strong>Tax Amount:</strong>
                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">
                {{ number_format($summary->tax_amount, 2) }} ETB
            </span>
                            </p>

                            <p><strong>Status:</strong>
                                <span class="capitalize">{{ $summary->status }}</span>
                            </p>
                            <p><strong>Period:</strong> {{ $summary->tax_period ?? 'N/A' }}</p>
                        </div>
                    </div>


                    {{-- FORM START --}}
                    <form method="POST" action="{{ route('admin.tax.calculate', $summary) }}" class="space-y-5">
                        @csrf

                        {{-- Taxable Income --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Taxable Income (ETB)
                            </label>
                            <input type="number" step="0.01" name="taxable_income"
                                   value="{{ old('taxable_income', $summary->taxable_income) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900
                                   dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        {{-- Rate + Deductible --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tax Rate (%)
                                </label>
                                <input type="number" step="0.01" name="tax_rate"
                                       value="{{ old('tax_rate', $summary->tax_rate) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900
                                       dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Deductible Amount (ETB)
                                </label>
                                <input type="number" step="0.01" name="deductible"
                                       value="{{ old('deductible', $summary->deductible) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900
                                       dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        {{-- Category + Period --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tax Category
                                </label>
                                <select name="category"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900
                                        dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select</option>
                                    <option value="A" {{ old('category', $summary->category) === 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('category', $summary->category) === 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C" {{ old('category', $summary->category) === 'C' ? 'selected' : '' }}>C</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tax Period
                                </label>
                                <input type="text" name="tax_period"
                                       value="{{ old('tax_period', $summary->tax_period) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900
                                       dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex justify-end gap-3">

                            {{-- BACK BUTTON --}}
                            <a href="{{ route('admin.tax.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200
       dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100
       text-sm font-medium rounded-md">
                                Back
                            </a>

                            {{-- MARK AS VERIFIED BUTTON --}}
                            <a href="{{ route('admin.tax.verify', $summary) }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700
       text-white text-sm font-semibold rounded-md">
                                Mark as Verified
                            </a>

                            {{-- CALCULATE TAX BUTTON --}}
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700
            text-white text-sm font-semibold rounded-md">
                                Calculate Tax
                            </button>

                        </div>



                    </form>
                    {{-- FORM END --}}
                    {{-- PREVIOUS TAX CALCULATIONS --}}
                    <div class="mt-10 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Previous Calculations
                        </h3>

                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead>
                            <tr class="text-left text-gray-600 dark:text-gray-300">
                                <th class="px-3 py-2">Income</th>
                                <th class="px-3 py-2">Rate</th>
                                <th class="px-3 py-2">Deductible</th>
                                <th class="px-3 py-2">Amount</th>
                                <th class="px-3 py-2">Period</th>
                                <th class="px-3 py-2">Status</th>
                            </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach (
                                \App\Models\TaxSummary::where('taxpayer_id', $summary->taxpayer_id)
                                    ->orderBy('created_at', 'desc')->get() as $prev
                            )
                                <tr class="text-gray-900 dark:text-gray-200">
                                    <td class="px-3 py-2">{{ number_format($prev->taxable_income, 2) }}</td>
                                    <td class="px-3 py-2">{{ $prev->tax_rate }}%</td>
                                    <td class="px-3 py-2">{{ number_format($prev->deductible, 2) }}</td>
                                    <td class="px-3 py-2">{{ number_format($prev->tax_amount, 2) }} ETB</td>
                                    <td class="px-3 py-2">{{ $prev->tax_period ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 capitalize">{{ $prev->status }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryField = document.querySelector("select[name='category']");
            const rateField = document.querySelector("input[name='tax_rate']");

            const rates = {
                A: 30,
                B: 15,
                C: 5
            };

            categoryField.addEventListener('change', function () {
                if (rates[this.value]) {
                    rateField.value = rates[this.value];
                }
            });
        });
    </script>

</x-app-layout>

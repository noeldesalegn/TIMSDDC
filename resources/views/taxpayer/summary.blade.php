<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tax Summary</h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="mb-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">Due Date</div>
                <div class="text-lg font-medium">{{ $due_date }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tax</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($breakdown as $row)
                            <tr>
                                <td class="px-4 py-2">{{ $row['category'] }}</td>
                                <td class="px-4 py-2">{{ number_format($row['amount'], 2) }}</td>
                                <td class="px-4 py-2">{{ number_format($row['rate'] * 100, 2) }}%</td>
                                <td class="px-4 py-2 font-medium">{{ number_format($row['tax'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <div class="text-lg font-semibold">Total Tax Due</div>
                <div class="text-2xl font-bold text-indigo-600">{{ number_format($total_tax, 2) }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

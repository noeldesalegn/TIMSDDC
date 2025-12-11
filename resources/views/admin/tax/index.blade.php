<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tax Summaries
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="px-4 py-2">Taxpayer</th>
                            <th class="px-4 py-2">Tax Type</th>
                            <th class="px-4 py-2">Tax Amount</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($summaries as $summary)
                            <tr class="text-gray-900 dark:text-gray-200">
                                <td class="px-4 py-2">{{ $summary->taxpayer->name }}</td>
                                <td class="px-4 py-2">{{ $summary->tax_type }}</td>
                                <td class="px-4 py-2">{{ $summary->tax_amount }} ETB</td>
                                <td class="px-4 py-2 capitalize">{{ $summary->status }}</td>

                                <td class="px-4 py-2">
                                    <a
                                        href="{{ route('admin.tax.edit', $summary) }}"
                                        class="text-indigo-600 hover:underline"
                                    >
                                        Calculate Tax
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $summaries->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>

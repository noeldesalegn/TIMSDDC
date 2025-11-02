<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Taxpayer Profile</h2>
            <a href="{{ route('admin.taxpayers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">Back</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                        <p class="text-sm mt-2">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->email_verified_at ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                            </span>
                        </p>
                    </div>
                    <div class="space-y-3">
                        <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <p class="text-xs text-blue-700 dark:text-blue-300">User ID</p>
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">#{{ $user->id }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-600 dark:text-gray-400">Joined</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ optional($user->created_at)->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tabs -->
        <div x-data="{tab: 'payments'}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="flex gap-4">
                    <button @click="tab='payments'" :class="tab==='payments' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-600 dark:text-gray-400'">Payments</button>
                    <button @click="tab='tax'" :class="tab==='tax' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-600 dark:text-gray-400'">Tax Summaries</button>
                    <button @click="tab='complaints'" :class="tab==='complaints' ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-gray-600 dark:text-gray-400'">Complaints</button>
                </nav>
            </div>
            <div class="p-6">
                <div x-show="tab==='payments'">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($payments as $p)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ optional($p->created_at)->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">ETB {{ number_format((float)$p->amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $p->status==='completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $payments->links() }}</div>
                </div>

                <div x-show="tab==='tax'">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tax Summaries</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($summaries as $s)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $s->tax_period ?? optional($s->created_at)->format('Y-m') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $s->tax_type }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">ETB {{ number_format((float)$s->tax_amount, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($s->status==='paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($s->status==='pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @endif">
                                            {{ ucfirst($s->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $summaries->links() }}</div>
                </div>

                <div x-show="tab==='complaints'">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Complaints</h3>
                    <div class="space-y-4">
                        @foreach ($complaints as $c)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $c->subject }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ optional($c->created_at)->format('M d, Y h:i A') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($c->status==='submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($c->status==='in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @endif">
                                    {{ ucfirst(str_replace('_',' ',$c->status)) }}
                                </span>
                            </div>
                            <p class="text-sm mt-3 text-gray-700 dark:text-gray-300">{{ $c->message }}</p>
                            @if($c->response)
                                <div class="mt-3 p-3 rounded bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                    <p class="text-xs text-blue-700 dark:text-blue-300">Response</p>
                                    <p class="text-sm text-blue-900 dark:text-blue-100">{{ $c->response }}</p>
                                </div>
                            @endif
                            <div class="mt-3">
                                <a href="{{ route('admin.complaints.show', $c) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Open</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">{{ $complaints->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

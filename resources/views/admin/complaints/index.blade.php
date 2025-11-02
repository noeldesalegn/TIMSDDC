<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Complaint Management</h2>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total: {{ $analytics['total'] }} • Submitted: {{ $analytics['submitted'] }} • In Progress: {{ $analytics['in_progress'] }} • Resolved: {{ $analytics['resolved'] }}</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ autoRefresh: false, timer: null, toggle(){ this.autoRefresh = !this.autoRefresh; if(this.autoRefresh){ this.timer = setInterval(()=>{ location.reload(); }, 30000); } else { clearInterval(this.timer); } } }">
        <!-- Filters -->
        <form method="GET" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input name="q" value="{{ $q }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="Subject or message" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="all" {{ $status==='all' ? 'selected' : '' }}>All</option>
                        <option value="submitted" {{ $status==='submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="in_progress" {{ $status==='in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $status==='resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Per Page</label>
                    <select name="per_page" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        @foreach ([10,25,50,100] as $pp)
                            <option value="{{ $pp }}" {{ (int)$perPage===$pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end justify-between">
                    <button class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">Apply</button>
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <input type="checkbox" @change="toggle()"> Auto-refresh (30s)
                    </label>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($complaints as $c)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">#{{ $c->id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $c->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $c->subject }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($c->status==='submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($c->status==='in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @endif">
                                    {{ ucfirst(str_replace('_',' ',$c->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ optional($c->created_at)->format('M d, Y h:i A') }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.complaints.show', $c) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Open</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $complaints->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

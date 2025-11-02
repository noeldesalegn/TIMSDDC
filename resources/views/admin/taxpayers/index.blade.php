<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Taxpayer Management</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.taxpayers.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                    Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{ selected: [], selectAll: false, confirmAction: null, showConfirm:false, setAction(a){this.confirmAction=a; this.showConfirm=true;} }" class="space-y-6">
        <!-- Filters -->
        <form method="GET" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input name="q" value="{{ $q }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="Name or Email" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">All</option>
                        <option value="verified" {{ $status==='verified' ? 'selected' : '' }}>Verified</option>
                        <option value="unverified" {{ $status==='unverified' ? 'selected' : '' }}>Unverified</option>
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
                <div class="flex items-end gap-3">
                    <a href="{{ route('admin.taxpayers.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700/60 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg">Clear</a>
                    <button class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">Apply</button>
                </div>
            </div>
        </form>

        <!-- Bulk actions -->
        <div class="flex items-center gap-3">
            <button type="button" @click="setAction('verify')" :disabled="selected.length===0" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50">Verify Selected</button>
            <button type="button" @click="setAction('unverify')" :disabled="selected.length===0" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg disabled:opacity-50">Unverify Selected</button>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-3 py-3">
                                <input type="checkbox" @change="selectAll = !selectAll; selected = selectAll ? @json($taxpayers->pluck('id')) : []">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Verified</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($taxpayers as $u)
                        <tr>
                            <td class="px-3 py-2">
                                <input type="checkbox" :value="{{ $u->id }}" @change="($event.target.checked) ? selected.push({{ $u->id }}) : selected = selected.filter(id => id !== {{ $u->id }})">
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $u->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $u->email_verified_at ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                    {{ $u->email_verified_at ? 'Verified' : 'Unverified' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ optional($u->created_at)->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.taxpayers.show', $u) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline mr-3">View</a>
                                <button type="button" class="text-gray-700 dark:text-gray-300 hover:underline" @click="document.getElementById('editModal-{{ $u->id }}').showModal()">Edit</button>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <dialog id="editModal-{{ $u->id }}" class="modal" role="dialog" aria-modal="true">
                            <form method="POST" action="{{ route('admin.taxpayers.update', $u) }}" class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-lg">
                                @csrf
                                @method('PATCH')
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Edit Taxpayer</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                        <input name="name" value="{{ $u->name }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required autofocus />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                        <input type="email" name="email" value="{{ $u->email }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="verify-{{ $u->id }}" name="verify" value="1" {{ $u->email_verified_at ? 'checked' : '' }}>
                                        <label for="verify-{{ $u->id }}" class="text-sm text-gray-700 dark:text-gray-300">Mark as verified</label>
                                    </div>
                                </div>
                                <div class="mt-6 flex gap-3 justify-end">
                                    <button type="button" onclick="this.closest('dialog').close()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Save</button>
                                </div>
                            </form>
                        </dialog>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $taxpayers->links() }}</div>
            </div>
        </div>

        <!-- Bulk confirm modal -->
        <dialog x-show="showConfirm" x-cloak class="modal" @close="showConfirm=false">
            <form method="POST" action="{{ route('admin.taxpayers.bulkVerify') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-lg">
                @csrf
                <input type="hidden" name="action" :value="confirmAction">
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Confirm Bulk Action</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to <span class="font-semibold" x-text="confirmAction"></span> the selected taxpayers?</p>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="showConfirm=false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">Confirm</button>
                </div>
            </form>
        </dialog>
    </div>
</x-app-layout>

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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ optional($u->created_at)->format('M d, Y') }}
        </span>
                                    <span class="text-xs text-gray-500">
            {{ optional($u->created_at)->format('h:i A') }}
        </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-3">

                                    <a href="{{ route('admin.taxpayers.show', $u) }}"
                                       class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200"
                                       title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>

                                    <button type="button"
                                            @click="document.getElementById('editModal-{{ $u->id }}').showModal()"
                                            class="px-3 py-1 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md transition duration-200">
                                        Edit
                                    </button>

                                    <a href="{{ route('admin.tax.create', $u) }}"
                                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow-sm hover:shadow-md transform active:scale-95 transition-all duration-150">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Create Summary
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <dialog id="editModal-{{ $u->id }}"
                                class="modal p-0 rounded-2xl bg-transparent backdrop:bg-gray-900/60 backdrop:backdrop-blur-sm"
                                role="dialog"
                                aria-modal="true">

                            <div class="fixed inset-0 flex items-center justify-center p-4">
                                <form method="POST" action="{{ route('admin.taxpayers.update', $u) }}"
                                      class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 dark:border-gray-800 transform transition-all">
                                    @csrf
                                    @method('PATCH')

                                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit Taxpayer</h3>
                                        <button type="button" onclick="this.closest('dialog').close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <div class="p-6 space-y-5">
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-black-500 dark:text-black-400 mb-2 ml-1">Full Name</label>
                                            <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                                                <input name="name" value="{{ $u->name }}"
                                                       class="w-full pl-10 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200"
                                                       placeholder="John Doe" required autofocus />
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-black-500 dark:text-black-400 mb-2 ml-1">Email Address</label>
                                            <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                                                <input type="email" name="email" value="{{ $u->email }}"
                                                       class="w-full pl-10 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200"
                                                       placeholder="john@example.com" required />
                                            </div>
                                        </div>

                                        <div class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-800/80 border border-gray-100 dark:border-gray-700">
                                            <input type="checkbox" id="verify-{{ $u->id }}" name="verify" value="1"
                                                   class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ $u->email_verified_at ? 'checked' : '' }}>
                                            <label for="verify-{{ $u->id }}" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Mark user as verified
                                                <span class="block text-xs text-gray-400 font-normal">This bypasses the email verification link.</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex gap-3 justify-end border-t border-gray-100 dark:border-gray-800">
                                        <button type="button" onclick="this.closest('dialog').close()"
                                                class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition duration-200">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                                class="px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-500/30 transition-all duration-200 active:scale-95">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </dialog>                        @endforeach
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

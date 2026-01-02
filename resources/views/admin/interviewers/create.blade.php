<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Add Interviewer
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.interviewers.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm mb-1">Name</label>
                            <input name="name" required
                                   class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Email</label>
                            <input type="email" name="email" required
                                   class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Password</label>
                            <input type="password" name="password" required
                                   class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>

                        <div class="flex gap-3">
                            <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                                Save
                            </button>
                            <a href="{{ route('admin.interviewers.index') }}"
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

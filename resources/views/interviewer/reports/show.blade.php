<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Report Details</h2>
            <a href="{{ route('interviewer.reports') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded">Back</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->title }}</h3>
                        <p class="text-sm text-gray-500">Category: {{ $report->category ?? '-' }}</p>
                        <p class="text-sm text-gray-500">Taxpayer: {{ optional($report->taxpayer)->email ?? '-' }}</p>
                        <div class="prose prose-sm dark:prose-invert max-w-none mt-4">{!! $report->body !!}</div>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('interviewer.reports.update', $report) }}" onsubmit="return confirm('Apply changes to this report?')">
                            @csrf
                            @method('PATCH')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm mb-1">Status</label>
                                    <select name="status" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        @foreach (['draft','submitted','approved','rejected'] as $s)
                                        <option value="{{ $s }}" {{ $report->status===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">Title</label>
                                    <input name="title" value="{{ $report->title }}" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">Category</label>
                                    <input name="category" value="{{ $report->category }}" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">Content (HTML)</label>
                                    <textarea name="body" rows="6" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ $report->body }}</textarea>
                                </div>
                                <div class="flex gap-3 justify-end">
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

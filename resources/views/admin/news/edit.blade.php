<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit News</h2>
            <a href="{{ route('admin.news.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">Back</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('admin.news.update', $news) }}" class="p-6" onsubmit="document.getElementById('body').value = document.getElementById('editor').innerHTML;">
                @csrf
                @method('PATCH')
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                        <input name="title" value="{{ old('title', $news->title) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required />
                        @error('title') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                        <div id="editor" contenteditable="true" class="w-full min-h-[200px] rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 p-3">{!! old('body', $news->body) !!}</div>
                        <input type="hidden" id="body" name="body" value="{!! old('body', $news->body) !!}">
                        @error('body') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $news->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Publish</label>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <a href="{{ route('admin.news.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded-lg">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

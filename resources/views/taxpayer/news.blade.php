<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">News & Updates</h2>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 border border-green-200 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($news as $item)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">{{ $item['title'] }}</h3>
                        <div class="text-sm text-gray-500">{{ $item['date'] }}</div>
                    </div>
                    <p class="mt-2">{{ $item['body'] }}</p>

                    <div class="mt-6">
                        <h4 class="font-medium mb-2">Comments</h4>
                        @php $list = $comments[$item['id']] ?? []; @endphp
                        @if (empty($list))
                            <p class="text-sm text-gray-600 dark:text-gray-400">No comments yet.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($list as $c)
                                    <div class="border rounded-md p-3">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="font-medium">{{ $c['author'] }}</span>
                                            <span class="text-gray-500">{{ $c['created_at'] }}</span>
                                        </div>
                                        <div class="mt-1 text-sm">{{ $c['message'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('taxpayer.news.comment', ['newsId' => $item['id']]) }}" class="mt-4 space-y-2">
                            @csrf
                            <textarea name="comment" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Write a comment..." required>{{ old('comment') }}</textarea>
                            @error('comment') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                            <button type="submit" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-700 disabled:opacity-25 transition">Post Comment</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-600 dark:text-gray-400">No news available.</p>
        @endforelse
    </div>
</x-app-layout>

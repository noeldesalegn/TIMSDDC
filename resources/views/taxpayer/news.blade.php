<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">News & Updates</h2>
            <div class="flex items-center gap-3">
                <!-- Search -->
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Search news..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                        id="newsSearch"
                    />
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="{ activeFilter: 'all' }" class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">Comment Posted!</h3>
                        <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- News Items -->
        @forelse ($news as $item)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition" 
                 x-data="{ 
                    likes: {{ rand(5, 50) }}, 
                    userLiked: false,
                    dislikes: {{ rand(2, 15) }},
                    userDisliked: false,
                    showComments: true,
                    commentText: ''
                 }"
                 data-search-text="{{ strtolower($item['title'] . ' ' . $item['body']) }}">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $item['title'] }}
                            </h3>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                @click="userLiked ? likes-- : likes++; userLiked = !userLiked; userDisliked && dislikes--; userDisliked = false"
                                class="flex items-center gap-1 px-3 py-2 rounded-lg transition"
                                :class="userLiked ? 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/10'"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM6 10.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM15.5 10.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"></path>
                                </svg>
                                <span x-text="likes"></span>
                            </button>
                            <button 
                                @click="userDisliked ? dislikes-- : dislikes++; userDisliked = !userDisliked; userLiked && likes--; userLiked = false"
                                class="flex items-center gap-1 px-3 py-2 rounded-lg transition"
                                :class="userDisliked ? 'bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                </svg>
                                <span x-text="dislikes"></span>
                            </button>
                        </div>
                    </div>

                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6">
                        {{ $item['body'] }}
                    </p>

                    <!-- Comments Section -->
                    <div x-show="showComments" class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Comments</h4>
                            <button @click="showComments = !showComments" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                <span x-show="!showComments">Show</span>
                                <span x-show="showComments">Hide</span> Comments
                            </button>
                        </div>

                        @php 
                            $commentsList = $comments[$item['id']] ?? [];
                        @endphp
                        
                        @if (empty($commentsList))
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">No comments yet. Be the first to comment!</p>
                        @else
                            <div class="space-y-4 mb-6">
                                @foreach ($commentsList as $comment)
                                    <div class="border-l-4 border-indigo-500 pl-4 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-r-lg">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $comment['author'] }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ $comment['message'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Comment Form -->
                        <form method="POST" action="{{ route('taxpayer.news.comment', ['newsId' => $item['id']]) }}" class="space-y-3">
                            @csrf
                            <textarea 
                                name="comment" 
                                rows="3" 
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                                placeholder="Write a comment..."
                                required
                            >{{ old('comment') }}</textarea>
                            @error('comment') 
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                            @enderror
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Comments are moderated</p>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
                <p class="text-gray-600 dark:text-gray-400">No news available at the moment.</p>
            </div>
        @endforelse

        <!-- Pagination Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center text-sm text-gray-600 dark:text-gray-400">
            Showing {{ count($news) }} news items
        </div>
    </div>

    <script>
        // Simple search functionality
        document.getElementById('newsSearch')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const newsItems = document.querySelectorAll('[data-search-text]');
            
            newsItems.forEach(item => {
                const text = item.getAttribute('data-search-text');
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Complaints</h2>
    </x-slot>

    <div class="grid gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                @if (session('success'))
                    <div class="rounded-md bg-green-50 p-4 border border-green-200 text-green-800 mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('taxpayer.complaints.submit') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Subject</label>
                        <input name="subject" value="{{ old('subject') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required />
                        @error('subject') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Message</label>
                        <textarea name="message" rows="5" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required>{{ old('message') }}</textarea>
                        @error('message') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-700 disabled:opacity-25 transition">
                            Submit Complaint
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Complaint History</h3>
                @if (empty($complaints))
                    <p class="text-sm text-gray-600 dark:text-gray-400">No complaints submitted yet.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($complaints as $c)
                            <div class="border rounded-md p-4">
                                <div class="flex items-center justify-between">
                                    <div class="font-medium">#{{ $c['id'] }} - {{ $c['subject'] }}</div>
                                    <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-700">{{ $c['status'] }}</span>
                                </div>
                                <div class="mt-2 text-sm whitespace-pre-line">{{ $c['message'] }}</div>
                                <div class="mt-2 text-xs text-gray-500">Submitted: {{ $c['created_at'] }}</div>
                                @if (!empty($c['response']))
                                    <div class="mt-3 p-3 rounded-md bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700">
                                        <div class="text-sm font-semibold">Response</div>
                                        <div class="text-sm">{{ $c['response'] }}</div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

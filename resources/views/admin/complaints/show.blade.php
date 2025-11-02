<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Complaint #{{ $complaint->id }}</h2>
            <a href="{{ route('admin.complaints.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg transition">Back</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">From</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $complaint->user->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $complaint->user->email ?? '' }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($complaint->status==='submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($complaint->status==='in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @endif">
                                {{ ucfirst(str_replace('_',' ',$complaint->status)) }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Subject</p>
                            <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $complaint->subject }}</p>
                        </div>
                        <div class="mt-4 space-y-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Message</p>
                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $complaint->message }}</p>
                        </div>
                        @if($complaint->attachment_path)
                            <div class="mt-4">
                                <a href="{{ Storage::url($complaint->attachment_path) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline" target="_blank">View Attachment</a>
                            </div>
                        @endif
                        @if($complaint->response)
                            <div class="mt-6 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                <p class="text-xs text-blue-700 dark:text-blue-300">Current Response</p>
                                <p class="text-sm text-blue-900 dark:text-blue-100 whitespace-pre-line">{{ $complaint->response }}</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" onsubmit="return confirm('Apply changes to this complaint?')">
                            @csrf
                            @method('PATCH')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        <option value="submitted" {{ $complaint->status==='submitted' ? 'selected' : '' }}>Submitted</option>
                                        <option value="in_progress" {{ $complaint->status==='in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="resolved" {{ $complaint->status==='resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                    @error('status') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Response</label>
                                    <textarea id="response" name="response" rows="6" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('response', $complaint->response) }}</textarea>
                                    @error('response') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Templates</label>
                                    <select onchange="document.getElementById('response').value = this.value" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                        <option value="">Select a template</option>
                                        <option>Thank you for your report. We are reviewing your complaint and will get back to you shortly.</option>
                                        <option>Your complaint is currently under investigation. We will provide an update within 3 business days.</option>
                                        <option>Your complaint has been resolved. Please review the resolution and let us know if further assistance is required.</option>
                                    </select>
                                </div>
                                <div class="flex gap-3 justify-end">
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Complaints & Support</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Success Message -->
        @if (session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">Complaint Submitted!</h3>
                        <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Complaint Submission Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Submit New Complaint</h3>
                        
                        <form method="POST" action="{{ route('taxpayer.complaints.submit') }}" class="space-y-6" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Complaint Category
                                </label>
                                <select name="category" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="">Select a category</option>
                                    <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical Issue</option>
                                    <option value="calculation" {{ old('category') === 'calculation' ? 'selected' : '' }}>Tax Calculation Error</option>
                                    <option value="service" {{ old('category') === 'service' ? 'selected' : '' }}>Service Quality</option>
                                    <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>Payment Issue</option>
                                    <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category') 
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Subject
                                </label>
                                <input 
                                    name="subject" 
                                    value="{{ old('subject') }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                                    placeholder="Brief summary of your complaint"
                                    required 
                                />
                                @error('subject') 
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Message -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Detailed Message
                                </label>
                                <textarea 
                                    name="message" 
                                    rows="6" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500" 
                                    placeholder="Please provide detailed information about your complaint..."
                                    required
                                >{{ old('message') }}</textarea>
                                @error('message') 
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- File Attachment -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Attach Supporting Documents (Optional)
                                </label>
                                <input 
                                    type="file" 
                                    name="attachment" 
                                    class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                />
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB)
                                </p>
                                @error('attachment') 
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Submit Complaint
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Complaint Statistics -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Your Complaints</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                <div class="flex items-center">
                                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg mr-3">
                                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Pending</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ collect($complaints ?? [])->where('status', 'submitted')->count() }} complaints</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">In Progress</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ collect($complaints ?? [])->where('status', 'in_progress')->count() }} complaints</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-3">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Resolved</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ collect($complaints ?? [])->where('status', 'resolved')->count() }} complaints</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint History -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Complaint History</h3>
                
                @if (empty($complaints))
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400">No complaints submitted yet.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($complaints as $complaint)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                #{{ $complaint['id'] ?? $loop->iteration }}
                                            </span>
                                            <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $complaint['subject'] }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Submitted: {{ \Carbon\Carbon::parse($complaint['created_at'] ?? now())->format('M d, Y h:i A') }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if(($complaint['status'] ?? 'submitted') === 'submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif(($complaint['status'] ?? '') === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif(($complaint['status'] ?? '') === 'resolved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $complaint['status'] ?? 'submitted')) }}
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                        {{ $complaint['message'] }}
                                    </p>
                                </div>
                                
                                @if (!empty($complaint['response']))
                                    <div class="mt-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                            <span class="text-sm font-semibold text-blue-900 dark:text-blue-300">Response from Support</span>
                                        </div>
                                        <p class="text-sm text-blue-800 dark:text-blue-300 whitespace-pre-line">
                                            {{ $complaint['response'] }}
                                        </p>
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

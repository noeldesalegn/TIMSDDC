<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Interviewer Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Taxpayer Info --}}
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div><strong>Name:</strong> {{ $taxpayer->name }}</div>
                <div><strong>Email:</strong> {{ $taxpayer->email }}</div>
                <div><strong>TIN:</strong> {{ $taxpayer->tin ?? 'Not provided' }}</div>
                <div>
                    <strong>Status:</strong>
                    <span class="px-2 py-1 rounded text-xs
                        {{ $taxpayer->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $taxpayer->email_verified_at ? 'Verified' : 'Unverified' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Uploaded Documents --}}
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Uploaded Documents</h3>

            @if($uploads->count())
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase">File</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase">Status</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @foreach($uploads as $upload)
                        <tr>
                            <td class="px-4 py-2">{{ $upload->filename }}</td>
                            <td class="px-4 py-2">{{ $upload->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded
                                        {{ $upload->status === 'processed'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($upload->status) }}
                                    </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">No uploads found for this taxpayer.</p>
            @endif
        </div>

        <a href="{{ route('interviewer.dashboard') }}"
           class="inline-block px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded">
            ← Back to Dashboard
        </a>

    </div>
</x-app-layout>

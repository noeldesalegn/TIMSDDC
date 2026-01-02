<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Interviewers</h2>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full border">
            <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Name</th>
                <th>Email</th>
                <th>Uploads</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($interviewers as $i)
                <tr class="border-t">
                    <td class="p-2">{{ $i->name }}</td>
                    <td>{{ $i->email }}</td>
                    <td>{{ $i->uploads_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('admin.interviewers.uploads', $i) }}"
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
                            View
                        </a>

                        @if($i->status === 'inactive')
                            <form method="POST" action="{{ route('admin.interviewers.enable', $i) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="inline-flex items-center px-3 py-1.5 border border-green-200 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 transition duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Enable
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.interviewers.destroy', $i) }}" class="inline" onsubmit="return confirm('Disable interviewer?')">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center px-3 py-1.5 border border-red-200 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    Disable
                                </button>
                            </form>
                        @endif
                    </td>                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>

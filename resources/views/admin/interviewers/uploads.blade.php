<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Uploads by {{ $interviewer->name }}
        </h2>
    </x-slot>

    <div class="p-6 space-y-6">
        @foreach($uploads as $taxpayerId => $group)
            <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
                <h3 class="font-semibold text-lg mb-3">
                    Taxpayer:
                    {{ $group->first()->taxpayer->name ?? 'Unknown Taxpayer' }}
                </h3>

                <ul class="space-y-2">
                    @foreach($group as $file)
                        <li class="flex justify-between text-sm">
                            <span>{{ $file->original_name }}</span>
                            <span class="text-gray-500">
                                {{ $file->created_at->format('Y-m-d') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</x-app-layout>

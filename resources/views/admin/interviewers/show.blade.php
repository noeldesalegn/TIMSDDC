<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Uploads by {{ $interviewer->name }}
        </h2>
    </x-slot>

    <div class="p-6 space-y-8">

        @foreach($uploads as $taxpayerName => $files)

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                    Taxpayer: {{ $taxpayerName }}
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($files as $file)
                        <div class="border rounded-lg overflow-hidden dark:border-gray-700">

                            {{-- FILE PREVIEW --}}
                            <div class="bg-gray-100 dark:bg-gray-900 h-48 flex items-center justify-center overflow-hidden">
                                @if(Str::startsWith($file->mime, 'image/'))
                                    <img
                                        src="{{ route('admin.uploads.view', $file->id) }}"
                                        class="object-cover w-full h-full"
                                        alt="Uploaded image"
                                    >
                                @elseif($file->mime === 'application/pdf')
                                    <iframe
                                        src="{{ route('admin.uploads.view', $file->id) }}"
                                        class="w-full h-full"
                                    ></iframe>
                                @else
                                    <div class="text-gray-500 text-sm uppercase">
                                        {{ strtoupper(pathinfo($file->original_name, PATHINFO_EXTENSION)) }}
                                    </div>
                                @endif
                            </div>

                            {{-- FILE INFO --}}
                            <div class="p-3 space-y-1 text-sm">
                                <p class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $file->original_name }}
                                </p>

                                <p class="text-gray-500">
                                    Uploaded by:
                                    <span class="font-medium text-gray-700 dark:text-gray-300">
                                        {{ $file->uploader->name }}
                                    </span>
                                </p>

                                <p class="text-gray-400 text-xs">
                                    {{ $file->created_at->format('d M Y, h:i A') }}
                                </p>

                                <div class="pt-2">
                                    <a href="{{ route('admin.uploads.view', $file->id) }}"
                                       target="_blank"
                                       class="inline-block px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs">
                                        Open
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>
</x-app-layout>

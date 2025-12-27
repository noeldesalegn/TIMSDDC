<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">File Uploads</h2>
            <a href="{{ route('interviewer.upload') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded text-gray-900 dark:text-gray-100">Refresh</a>
        </div>
    </x-slot>

    <div x-data="uploadPage()" x-init="init()" class="space-y-6">
        <template x-if="!online">
            <div class="p-3 rounded bg-yellow-100 text-yellow-900 dark:bg-yellow-900/30 dark:text-yellow-200">
                You are offline. You can stage files, but uploading will start once you are online.
            </div>
        </template>

        <!-- Drop zone -->
{{--        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">--}}
{{--            <div class="p-6">--}}
{{--                <div @dragover.prevent @dragenter.prevent @drop.prevent="handleDrop($event)" class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900 border-gray-300 dark:border-gray-700">--}}
{{--                    <p class="text-gray-700 dark:text-gray-300 mb-2">Drag & drop files here to upload</p>--}}
{{--                    <p class="text-xs text-gray-500">Accepted: PDF, images, DOC/XLS, CSV, TXT (≤ 10MB each)</p>--}}
{{--                    <input type="file" multiple class="hidden" x-ref="fileInput" @change="handleFiles($event.target.files)">--}}
{{--                    <div class="mt-3">--}}
{{--                        <button type="button" @click="$refs.fileInput.click()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Select Files</button>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="mt-4">--}}
{{--                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload ZIP archive</label>--}}
{{--                    <input type="file" accept=".zip" @change="setZip($event)" class="block w-full text-sm text-gray-700 dark:text-gray-300" />--}}
{{--                </div>--}}

{{--                <div class="mt-6" x-show="queue.length">--}}
{{--                    <h3 class="text-sm font-semibold mb-2">Queued Files (<span x-text="queue.length"></span>)</h3>--}}
{{--                    <ul class="space-y-2">--}}
{{--                        <template x-for="(f, idx) in queue" :key="idx">--}}
{{--                            <li class="flex items-center justify-between p-3 rounded border border-gray-200 dark:border-gray-700">--}}
{{--                                <div class="truncate"><span class="font-medium" x-text="f.name"></span> <span class="text-xs text-gray-500" x-text="human(f.size)"></span></div>--}}
{{--                                <div class="w-1/3">--}}
{{--                                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded">--}}
{{--                                        <div class="h-2 bg-green-600 rounded" :style="`width:${progress[f.name]||0}%`"></div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </li>--}}
{{--                        </template>--}}
{{--                    </ul>--}}
{{--                    <div class="mt-3 flex gap-3">--}}
{{--                        <button type="button" @click="startUpload()" :disabled="!online || uploading" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded disabled:opacity-50">Start Upload</button>--}}
{{--                        <button type="button" @click="clearQueue()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded">Clear</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <!-- Filters -->
        <form method="GET" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <input name="q" value="{{ $q }}" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" placeholder="Filename" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                    <select name="type" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">All</option>
                        <option value="image" {{ $type==='image' ? 'selected' : '' }}>Images</option>
                        <option value="pdf" {{ $type==='pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="doc" {{ $type==='doc' ? 'selected' : '' }}>Documents</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Per Page</label>
                    <select name="per_page" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        @foreach ([12,24,48] as $pp)
                            <option value="{{ $pp }}" {{ (int)$perPage===$pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded">Apply</button>
                </div>
            </div>
        </form>

        <!-- Upload history -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($uploads->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($uploads as $u)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <div class="p-3">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $u->original_name }}</p>
                            <p class="text-xs text-gray-500">{{ strtoupper($u->mime ?? 'file') }} • {{ number_format((int)$u->size/1024,0) }} KB</p>
                        </div>
                        @php $url = asset('storage/'.$u->path); @endphp
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Uploaded by:
                            <span class="font-medium">
                            {{ $u->uploader?->name ?? 'Unknown' }}
                        </span>
                        </p>

                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Taxpayer:
                            <span class="font-medium">
                            {{ $u->taxpayer?->name ?? 'N/A' }}
                        </span>
                        </p>
                        @if(isset($u->mime) && strpos($u->mime, 'image/') === 0)
                            <img src="{{ $url }}" alt="File preview" loading="lazy" width="640" height="320" class="w-full h-40 object-cover">
                        @elseif(($u->mime ?? '')==='application/pdf')
                            <div class="h-40 flex items-center justify-center text-gray-500">PDF</div>
                        @else
                            <div class="h-40 flex items-center justify-center text-gray-500">FILE</div>
                        @endif
                        <div class="p-3 flex gap-3">
                            <a href="{{ route('interviewer.upload.download', $u) }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">Download</a>
                            <form method="POST" action="{{ route('interviewer.upload.destroy', $u) }}" onsubmit="return confirm('Delete this file?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $uploads->links() }}</div>
                @else
                <div class="text-sm text-gray-500">No uploads found.</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function uploadPage(){
            return {
                queue: [],
                progress: {},
                zip: null,
                uploading: false,
                online: navigator.onLine,
                init(){
                    window.addEventListener('online', ()=>{ this.online = true; });
                    window.addEventListener('offline', ()=>{ this.online = false; });
                },
                human(bytes){
                    if (bytes < 1024) return bytes + ' B';
                    if (bytes < 1024*1024) return Math.round(bytes/1024)+' KB';
                    return (bytes/1024/1024).toFixed(1)+' MB';
                },
                handleDrop(e){
                    this.handleFiles(e.dataTransfer.files);
                },
                handleFiles(files){
                    const max = 10*1024*1024;
                    const allowed = ['application/pdf','image/jpeg','image/png','image/gif','image/webp','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','text/plain','text/csv'];
                    [...files].forEach(f=>{
                        if (f.size <= max && (allowed.includes(f.type) || f.name.toLowerCase().endsWith('.zip'))) {
                            this.queue.push(f);
                            this.progress[f.name]=0;
                        }
                    });
                },
                setZip(e){
                    const f = e.target.files[0];
                    if (f && f.name.toLowerCase().endsWith('.zip')){
                        this.queue.push(f);
                        this.progress[f.name]=0;
                    }
                    e.target.value = '';
                },
                clearQueue(){
                    this.queue=[]; this.progress={};
                },
                async startUpload(){
                    if (!this.online || this.uploading) return;
                    this.uploading = true;
                    for (const file of this.queue){
                        await this.uploadOne(file);
                    }
                    this.uploading = false;
                    this.queue=[]; this.progress={};
                    window.location.reload();
                },
                uploadOne(file){
                    return new Promise((resolve)=>{
                        const form = new FormData();
                        if (file.name.toLowerCase().endsWith('.zip')) form.append('zip', file);
                        else form.append('files[]', file);
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route('interviewer.upload.store') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                        xhr.upload.addEventListener('progress', (e)=>{
                            if (e.lengthComputable){ this.progress[file.name] = Math.round((e.loaded/e.total)*100); }
                        });
                        xhr.onreadystatechange = ()=>{
                            if (xhr.readyState===4){ resolve(); }
                        };
                        xhr.send(form);
                    });
                }
            }
        }
    </script>
</x-app-layout>

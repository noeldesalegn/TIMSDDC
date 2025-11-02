<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">New Income Report</h2>
            <div class="flex items-center gap-3">
                <button type="button" @click="loadDraft()" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded">Load Draft</button>
                <button type="button" @click="saveLocal()" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Save Locally</button>
            </div>
        </div>
    </x-slot>

    <div x-data="reportCreate()" x-init="init()" class="space-y-6">
        <template x-if="!online">
            <div class="p-3 rounded bg-yellow-100 text-yellow-900 dark:bg-yellow-900/30 dark:text-yellow-200">
                You are offline. You can fill the form and save locally. Submit when online.
            </div>
        </template>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form id="reportForm" method="POST" action="{{ route('interviewer.reports.store') }}" class="p-6" @submit="beforeSubmit($event)">
                @csrf
                <input type="hidden" name="status" x-model="form.status">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm mb-2">Title</label>
                        <input name="title" x-model="form.title" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required />
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Category</label>
                        <select name="category" x-model="form.category" @change="applyTemplate()" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Select category</option>
                            <option>Employment</option>
                            <option>Business</option>
                            <option>Rental</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Taxpayer Email (optional)</label>
                        <input type="email" name="taxpayer_email" x-model="form.taxpayer_email" placeholder="taxpayer@example.com" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Taxpayer (ID, optional)</label>
                        <input type="number" name="taxpayer_id" x-model="form.taxpayer_id" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm mb-2">Content</label>
                        <div id="editor" contenteditable="true" class="min-h-[220px] w-full rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 p-3" x-html="form.body"></div>
                        <input type="hidden" id="body" name="body" :value="form.body" />
                        <p class="mt-2 text-xs text-gray-500">Tip: You can paste formatted content. It will be submitted as HTML.</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button type="button" @click="clearForm()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded">Clear</button>
                    <button type="button" @click="setStatus('draft'); submit()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded">Save as Draft</button>
                    <button type="button" @click="setStatus('submitted'); submit()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function reportCreate(){
            return {
                online: navigator.onLine,
                form: { title:'', category:'', taxpayer_email:'', taxpayer_id:'', body:'', status:'draft' },
                init(){
                    window.addEventListener('online', ()=>{ this.online = true; });
                    window.addEventListener('offline', ()=>{ this.online = false; });
                    const saved = localStorage.getItem('reportDraft');
                    if (saved){ try { this.form = { ...this.form, ...JSON.parse(saved) }; this.$nextTick(()=> this.syncEditor()); } catch(e){} }
                    document.getElementById('editor').addEventListener('input', ()=>{ this.form.body = document.getElementById('editor').innerHTML; });
                },
                applyTemplate(){
                    if (!this.form.body) {
                        if (this.form.category==='Employment') this.form.body = '<h3>Employment Income Verification</h3><p>Employer:</p><p>Position:</p><p>Gross Income:</p><p>Deductions:</p>';
                        else if (this.form.category==='Business') this.form.body = '<h3>Business Income Verification</h3><p>Business Name:</p><p>Industry:</p><p>Gross Income:</p><p>Expenses:</p>';
                        else if (this.form.category==='Rental') this.form.body = '<h3>Rental Income Verification</h3><p>Property Address:</p><p>Rental Period:</p><p>Gross Rent:</p><p>Expenses:</p>';
                        this.syncEditor();
                    }
                },
                syncEditor(){ document.getElementById('editor').innerHTML = this.form.body; document.getElementById('body').value = this.form.body; },
                clearForm(){ this.form = { title:'', category:'', taxpayer_email:'', taxpayer_id:'', body:'', status:'draft' }; this.syncEditor(); },
                saveLocal(){ localStorage.setItem('reportDraft', JSON.stringify(this.form)); alert('Saved locally'); },
                loadDraft(){ const s = localStorage.getItem('reportDraft'); if (s){ this.form = { ...this.form, ...JSON.parse(s) }; this.syncEditor(); } },
                setStatus(s){ this.form.status = s; },
                submit(){ document.getElementById('body').value = document.getElementById('editor').innerHTML; document.getElementById('reportForm').submit(); },
                beforeSubmit(e){ this.form.body = document.getElementById('editor').innerHTML; document.getElementById('body').value = this.form.body; }
            }
        }
    </script>
</x-app-layout>

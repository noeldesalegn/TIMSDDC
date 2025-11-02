<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Report Generation</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.export', ['type' => $type, 'start' => $start, 'end' => $end, 'tax_type' => $taxType, 'category' => $category]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">Export CSV</a>
            </div>
        </div>
    </x-slot>

    <script>
        function reportsPage() {
            return {
                form: { type: '{{ $type }}', start: '{{ $start }}', end: '{{ $end }}', tax_type: '{{ $taxType }}', category: '{{ $category }}' },
                kpis: { total: 0, paid: 0, pending: 0, overdue: 0 },
                chart: null,
                loading: false,
                cache: {},
                async initFromServer() {
                    await this.waitForChart();
                    this.generate();
                },
                waitForChart() {
                    return new Promise((resolve) => {
                        if (window.Chart) return resolve();
                        let tries = 0;
                        const timer = setInterval(() => {
                            if (window.Chart || tries++ > 200) { // up to ~10s
                                clearInterval(timer);
                                resolve();
                            }
                        }, 50);
                    });
                },
                preset(name) {
                    const today = new Date();
                    const pad = n => String(n).padStart(2, '0');
                    const fmt = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
                    if (name==='today') {
                        this.form.start = fmt(today); this.form.end = fmt(today);
                    } else if (name==='week') {
                        const first = new Date(today); first.setDate(today.getDate()-today.getDay());
                        this.form.start = fmt(first); this.form.end = fmt(today);
                    } else if (name==='month') {
                        const first = new Date(today.getFullYear(), today.getMonth(), 1);
                        this.form.start = fmt(first); this.form.end = fmt(today);
                    } else if (name==='year') {
                        const first = new Date(today.getFullYear(), 0, 1);
                        this.form.start = fmt(first); this.form.end = fmt(today);
                    }
                },
                dataUrl() {
                    const p = new URLSearchParams(this.form).toString();
                    return `{{ route('admin.reports.data') }}?${p}`;
                },
                exportUrl() {
                    const p = new URLSearchParams(this.form).toString();
                    return `{{ route('admin.reports.export') }}?${p}`;
                },
                async generate() {
                    this.loading = true;
                    const key = JSON.stringify(this.form);
                    try {
                        let data;
                        if (this.cache[key]) {
                            data = this.cache[key];
                        } else {
                            const res = await fetch(this.dataUrl());
                            data = await res.json();
                            this.cache[key] = data;
                        }
                        if (this.form.type==='compliance') {
                            this.kpis = { total: data.total, paid: data.paid, pending: data.pending, overdue: data.overdue };
                            const labels = ['Paid','Pending','Overdue'];
                            const values = [data.paid, data.pending, data.overdue];
                            this.renderChart('bar', labels, values, 'Compliance Counts');
                        } else {
                            this.kpis = { ...this.kpis, total: data.total };
                            const labels = (data.series || []).map(x => x.period);
                            const values = (data.series || []).map(x => parseFloat(x.total));
                            this.renderChart('line', labels, values, 'Amount (ETB)');
                        }
                    } catch (e) {
                        console.error('Failed to load report data', e);
                        window?.dispatchEvent(new CustomEvent('toast', { detail: { type:'error', message: 'Failed to load report data' }}));
                    } finally {
                        this.loading = false;
                    }
                },
                renderChart(type, labels, data, label) {
                    const canvas = document.getElementById('reportChart');
                    if (!canvas || !window.Chart) return; // safeguard
                    const ctx = canvas.getContext('2d');
                    if (this.chart) { this.chart.destroy(); }
                    this.chart = new Chart(ctx, {
                        type,
                        data: { labels, datasets: [{ label, data, borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,0.2)' }] },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }
            }
        }
    </script>

    <div x-data="reportsPage()" x-init="initFromServer()" class="space-y-6">
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
                    <select x-model="form.type" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="tax_collection">Tax Collection</option>
                        <option value="compliance">Compliance</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                    <input type="date" x-model="form.start" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                    <input type="date" x-model="form.end" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tax Type</label>
                    <select x-model="form.tax_type" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">All</option>
                        <option>Employment</option>
                        <option>Business</option>
                        <option>Rental</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select x-model="form.category" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">All</option>
                        <option>A</option>
                        <option>B</option>
                        <option>C</option>
                    </select>
                </div>
            </div>
            <div class="px-6 pb-6 flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Presets:</span>
                    <button @click="preset('today')" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded">Today</button>
                    <button @click="preset('week')" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded">This Week</button>
                    <button @click="preset('month')" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded">This Month</button>
                    <button @click="preset('year')" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded">This Year</button>
                </div>
                <div class="ml-auto flex items-center gap-3">
                    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded">Clear</a>
                    <a :href="exportUrl()" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">Export</a>
                    <button @click="generate()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">Generate</button>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Primary Total</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100" x-text="kpis.total"></p>
                </div>
            </div>
            <template x-if="form.type==='compliance'">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Paid</p>
                        <p class="text-2xl font-semibold text-green-600 dark:text-green-400" x-text="kpis.paid"></p>
                    </div>
                </div>
            </template>
            <template x-if="form.type==='compliance'">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                        <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400" x-text="kpis.pending"></p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Chart -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 relative" :aria-busy="loading.toString()">
                <canvas id="reportChart" height="90"></canvas>
                <div x-show="loading" x-cloak class="absolute inset-0 flex items-center justify-center bg-white/60 dark:bg-gray-900/60">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-app-layout>

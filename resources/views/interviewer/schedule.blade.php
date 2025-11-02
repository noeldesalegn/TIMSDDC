<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">My Schedule</h2>
            <div class="flex items-center gap-3">
                <button type="button" @click="requestNotify()" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">Enable Reminders</button>
            </div>
        </div>
    </x-slot>

    <div x-data="schedulePage()" x-init="init()" class="space-y-6">
        <!-- Create appointment -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">New Appointment</h3>
                <form @submit.prevent="create()" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Title</label>
                        <input x-model="form.title" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Start</label>
                        <input type="datetime-local" x-model="form.start_at" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1">End</label>
                        <input type="datetime-local" x-model="form.end_at" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Taxpayer Email (optional)</label>
                        <input type="email" x-model="form.taxpayer_email" placeholder="taxpayer@example.com" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Location</label>
                        <input x-model="form.location" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Contact Phone</label>
                        <input x-model="form.contact_phone" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm mb-1">Notes</label>
                        <textarea x-model="form.notes" rows="2" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></textarea>
                    </div>
                    <div class="md:col-span-3 flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Create</button>
                        <button type="button" @click="resetForm()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 rounded">Clear</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Calendar -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        function schedulePage(){
            return {
                calendar: null,
                notify: false,
                form: { title:'', start_at:'', end_at:'', taxpayer_email:'', location:'', contact_phone:'', notes:'' },
                async init(){
                    await this.waitForCalendar();
                    this.renderCalendar();
                    this.scanUpcoming();
                    setInterval(()=>this.scanUpcoming(), 60000);
                },
                waitForCalendar(){
                    return new Promise((resolve)=>{
                        if (window.FullCalendar && window.FullCalendar.Calendar) return resolve();
                        let tries = 0;
                        const timer = setInterval(()=>{
                            if ((window.FullCalendar && window.FullCalendar.Calendar) || tries++ > 200){
                                clearInterval(timer);
                                resolve();
                            }
                        }, 50);
                    });
                },
                renderCalendar(){
                    const el = document.getElementById('calendar');
                    const calendar = new FullCalendar.Calendar(el, {
                        initialView: 'timeGridWeek',
                        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
                        height: 700,
                        businessHours: { daysOfWeek:[1,2,3,4,5], startTime:'09:00', endTime:'17:00' },
                        selectable: true,
                        editable: true,
                        eventSources: [{ url: '{{ route('interviewer.schedule.events') }}', method:'GET' }],
                        select: (info)=>{
                            this.form.start_at = info.startStr.substring(0,16);
                            this.form.end_at = info.endStr.substring(0,16);
                        },
                        eventDrop: (info)=>{ this.updateEventTimes(info.event); },
                        eventResize: (info)=>{ this.updateEventTimes(info.event); },
                        eventClick: (info)=>{ this.openEventMenu(info.event); },
                    });
                    calendar.render();
                    this.calendar = calendar;
                },
                resetForm(){ this.form = { title:'', start_at:'', end_at:'', taxpayer_email:'', location:'', contact_phone:'', notes:'' }; },
                async create(){
                    try{
                        const res = await fetch('{{ route('interviewer.schedule.store') }}', {
                            method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
                            body: JSON.stringify(this.form)
                        });
                        if (!res.ok) throw new Error('Create failed');
                        this.resetForm();
                        this.calendar.refetchEvents();
                    }catch(e){ alert('Failed to create appointment'); }
                },
                async updateEventTimes(event){
                    try{
                        await fetch(`{{ url('/interviewer/schedule') }}/${event.id}`, {
                            method:'PATCH', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
                            body: JSON.stringify({ start_at: event.start.toISOString(), end_at: event.end ? event.end.toISOString() : event.start.toISOString() })
                        });
                    }catch(e){ console.error(e); }
                },
                async openEventMenu(event){
                    const action = prompt('Type: cancel, complete, or edit title', '');
                    if (!action) return;
                    if (action==='cancel' || action==='complete'){
                        const status = action==='cancel' ? 'cancelled' : 'completed';
                        await fetch(`{{ url('/interviewer/schedule') }}/${event.id}`, { method:'PATCH', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' }, body: JSON.stringify({ status }) });
                        this.calendar.refetchEvents();
                    } else {
                        await fetch(`{{ url('/interviewer/schedule') }}/${event.id}`, { method:'PATCH', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}' }, body: JSON.stringify({ title: action }) });
                        this.calendar.refetchEvents();
                    }
                },
                requestNotify(){
                    if (!('Notification' in window)) return alert('Notifications not supported');
                    Notification.requestPermission().then(p => { this.notify = (p==='granted'); });
                },
                async scanUpcoming(){
                    if (!this.notify || !('Notification' in window)) return;
                    const now = new Date();
                    const next = new Date(now.getTime() + 15*60000);
                    try{
                        const params = new URLSearchParams({ start: now.toISOString(), end: next.toISOString() });
                        const res = await fetch('{{ route('interviewer.schedule.events') }}?'+params.toString());
                        const events = await res.json();
                        events.forEach(ev => {
                            new Notification('Upcoming appointment', { body: `${ev.title} at ${new Date(ev.start).toLocaleTimeString()}` });
                        });
                    }catch(e){}
                }
            }
        }
    </script>
</x-app-layout>

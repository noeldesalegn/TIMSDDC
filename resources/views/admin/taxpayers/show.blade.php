<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Taxpayer Profile
            </h2>
            <a href="{{ route('admin.taxpayers.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                Back
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ================= PROFILE CARD ================= --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="grid md:grid-cols-3 gap-6">

                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>

                    <div class="mt-3">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $user->email_verified_at
                                ? 'bg-green-100 text-green-800'
                                : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded">
                        <p class="text-xs text-gray-500">User ID</p>
                        <p class="font-semibold">#{{ $user->id }}</p>
                    </div>

                    <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded">
                        <p class="text-xs text-gray-500">Joined</p>
                        <p class="font-semibold">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= TIN SECTION ================= --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">TIN Information</h3>

            <div class="grid md:grid-cols-2 gap-6">

                {{-- TIN DETAILS --}}
                <div class="space-y-2">
                    <p><strong>TIN Number:</strong> {{ $user->tin ?? '—' }}</p>

                    <p>
                        <strong>Status:</strong>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($user->tin_status === 'approved') bg-green-100 text-green-800
                            @elseif($user->tin_status === 'rejected') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($user->tin_status ?? 'pending') }}
                        </span>
                    </p>
                </div>

                {{-- TIN DOCUMENT --}}
                <div>
                    @if($user->tin_document)
                        <button
                            onclick="document.getElementById('tinModal').showModal()"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Preview TIN Document
                        </button>
                    @else
                        <p class="text-gray-500 text-sm">No TIN document uploaded.</p>
                    @endif
                </div>
            </div>

            {{-- ADMIN ACTIONS --}}
            @if($user->tin_status !== 'approved')
                <div class="mt-6 flex gap-3">

                    <form method="POST" action="{{ route('admin.taxpayers.tin.approve', $user) }}">
                        @csrf
                        @method('PATCH')
                        <button class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Approve TIN
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.taxpayers.tin.reject', $user) }}">
                        @csrf
                        @method('PATCH')
                        <button class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Reject TIN
                        </button>
                    </form>

                </div>
            @endif
        </div>

        {{-- ================= HISTORY TABS ================= --}}
        <div x-data="{tab:'payments'}" class="bg-white dark:bg-gray-800 shadow rounded-lg">

            <div class="border-b p-4 flex gap-4">
                <button @click="tab='payments'" :class="tab==='payments' ? 'font-bold text-indigo-600' : ''">Payments</button>
                <button @click="tab='tax'" :class="tab==='tax' ? 'font-bold text-indigo-600' : ''">Tax Summaries</button>
                <button @click="tab='complaints'" :class="tab==='complaints' ? 'font-bold text-indigo-600' : ''">Complaints</button>
            </div>

            <div class="p-6">

                {{-- PAYMENTS --}}
                <div x-show="tab==='payments'">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-500">
                            <th>Date</th><th>Amount</th><th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $p)
                            <tr class="border-t">
                                <td>{{ $p->created_at->format('M d, Y') }}</td>
                                <td>ETB {{ number_format($p->amount,2) }}</td>
                                <td>{{ ucfirst($p->status) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $payments->links() }}</div>
                </div>

                {{-- TAX SUMMARIES --}}
                <div x-show="tab==='tax'">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="text-left text-gray-500">
                            <th>Period</th><th>Type</th><th>Amount</th><th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($summaries as $s)
                            <tr class="border-t">
                                <td>{{ $s->tax_period }}</td>
                                <td>{{ $s->tax_type }}</td>
                                <td>ETB {{ number_format($s->tax_amount,2) }}</td>
                                <td>{{ ucfirst($s->status) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $summaries->links() }}</div>
                </div>

                {{-- COMPLAINTS --}}
                <div x-show="tab==='complaints'">
                    @foreach($complaints as $c)
                        <div class="border rounded p-4 mb-4">
                            <p class="font-semibold">{{ $c->subject }}</p>
                            <p class="text-xs text-gray-500">{{ $c->created_at->format('M d, Y') }}</p>
                            <p class="mt-2">{{ $c->message }}</p>
                        </div>
                    @endforeach
                    {{ $complaints->links() }}
                </div>

            </div>
        </div>

    </div>

    {{-- ================= TIN PREVIEW MODAL ================= --}}
    <dialog id="tinModal" class="rounded-xl w-full max-w-3xl backdrop:bg-black/60">
        <div class="p-4 bg-white dark:bg-gray-900 rounded-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-lg">TIN Document</h3>
                <button onclick="tinModal.close()">✕</button>
            </div>

            @php
                $ext = pathinfo($user->tin_document, PATHINFO_EXTENSION);
            @endphp

            @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                <img src="{{ asset('storage/'.$user->tin_document) }}" class="w-full rounded">
            @else
                <iframe src="{{ asset('storage/'.$user->tin_document) }}"
                        class="w-full h-[70vh] rounded"></iframe>
            @endif
        </div>
    </dialog>

</x-app-layout>

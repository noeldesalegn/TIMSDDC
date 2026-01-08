<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            TIN Verification
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6 space-y-4">

        {{-- APPROVED --}}
        @if ($user->tin_status === 'approved')
            <div class="p-4 bg-green-100 text-green-800 rounded">
                <strong>TIN Approved</strong><br>
                Your TIN has been verified successfully.
            </div>

            {{-- PENDING --}}
        @elseif ($user->tin_status === 'pending')
            <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
                <strong>TIN Pending</strong><br>
                Your TIN is under review. Please wait for admin approval.
            </div>

            {{-- REJECTED --}}
        @elseif ($user->tin_status === 'rejected')
            <div class="p-4 bg-red-100 text-red-800 rounded">
                <strong>TIN Rejected</strong><br>
                Reason: {{ $user->tin_rejection_reason ?? 'No reason provided.' }}
            </div>

            {{-- RESUBMISSION FORM --}}
            <form method="POST"
                  action="{{ route('taxpayer.tin.submit') }}"
                  enctype="multipart/form-data"
                  class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                @csrf

                <div>
                    <x-input-label value="TIN Number" />
                    <x-text-input
                        name="tin"
                        value="{{ old('tin', $user->tin) }}"
                        class="block w-full mt-1"
                        required
                    />
                    <x-input-error :messages="$errors->get('tin')" />
                </div>

                <div>
                    <x-input-label value="TIN Document (PDF or Image)" />
                    <input
                        type="file"
                        name="tin_document"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full mt-1 text-sm"
                        required
                    >
                    <x-input-error :messages="$errors->get('tin_document')" />
                </div>

                <div class="flex justify-end">
                    <x-primary-button>
                        Resubmit TIN
                    </x-primary-button>
                </div>
            </form>

            {{-- NO TIN YET (OPTIONAL FIRST SUBMISSION) --}}
        @else
            <div class="p-4 bg-gray-100 text-gray-700 rounded">
                <strong>No TIN Submitted</strong><br>
                Please submit your TIN for verification.
            </div>

            <form method="POST"
                  action="{{ route('taxpayer.tin.submit') }}"
                  enctype="multipart/form-data"
                  class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                @csrf

                <div>
                    <x-input-label value="TIN Number" />
                    <x-text-input
                        name="tin"
                        class="block w-full mt-1"
                        required
                    />
                </div>

                <div>
                    <x-input-label value="TIN Document (PDF or Image)" />
                    <input
                        type="file"
                        name="tin_document"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full mt-1 text-sm"
                        required
                    >
                </div>

                <div class="flex justify-end">
                    <x-primary-button>
                        Submit TIN
                    </x-primary-button>
                </div>
            </form>
        @endif

    </div>
</x-app-layout>

<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input id="name" name="name" class="block mt-1 w-full" required />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" required />
        </div>

        <!-- TIN -->
        <div class="mt-4">
            <x-input-label for="tin" value="TIN Number" />
            <x-text-input id="tin" name="tin" class="block mt-1 w-full" />
        </div>

        <!-- TIN Document -->
        <div class="mt-4">
            <x-input-label for="tin_document" value="TIN Document (PDF or Image)" />
            <input
                type="file"
                name="tin_document"
                accept=".pdf,.jpg,.jpeg,.png"
                class="block w-full mt-1 text-sm"
            />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" name="password_confirmation" type="password" required />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Register
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

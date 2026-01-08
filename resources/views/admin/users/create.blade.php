<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
            Register Staff User
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.users.store') }}"
              class="bg-white dark:bg-gray-800 p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <x-input-label value="Full Name" />
                <x-text-input name="name" class="block w-full mt-1" required />
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input-label value="Email Address" />
                <x-text-input type="email" name="email" class="block w-full mt-1" required />
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input-label value="Password" />
                <x-text-input type="password" name="password" class="block w-full mt-1" required />
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input-label value="Confirm Password" />
                <x-text-input type="password" name="password_confirmation" class="block w-full mt-1" required />
                @error('password_confirmation')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input-label value="User Role" />
                <select name="role"
                        class="block w-full mt-1 rounded border-gray-300 dark:bg-gray-700 dark:text-white"
                        required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">Admin</option>
                    <option value="cashier">Cashier</option>
                    <option value="interviewer">Interviewer</option>
                </select>
                @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <x-primary-button>Create User</x-primary-button>
            </div>
        </form>

    </div>
</x-app-layout>

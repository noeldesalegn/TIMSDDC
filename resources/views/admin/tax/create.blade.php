<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Create Tax Summary for {{ $user->name }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
        <form method="POST" action="{{ route('admin.tax.store', $user) }}">
            @csrf

            <div class="mb-4">
                <label class="block font-medium">Tax Type</label>
                <select name="tax_type" class="w-full rounded border">
                    <option value="Employment">Employment</option>
                    <option value="Business">Business</option>
                    <option value="Rental">Rental</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Category</label>
                <select name="category" class="w-full rounded border">
                    <option value="">N/A</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Tax Period</label>
                <input name="tax_period" required
                       placeholder="2024/2025"
                       class="w-full rounded border"/>
            </div>

            <div class="flex justify-end">
                <button class="px-4 py-2 bg-green-600 text-white rounded">
                    Create Tax Summary
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

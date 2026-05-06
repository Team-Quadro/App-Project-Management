@section('title', 'Welcome')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-gray-900">Welcome</h1>
    </x-slot>

    <div class="max-w-3xl">
        <h2 class="text-lg font-semibold text-gray-900">Choose how you want to continue</h2>
        <p class="text-sm text-gray-500 mt-1">Register your subsidiary as a PIC or join an existing subsidiary as a regular user.</p>
    </div>

    @if ($pendingTenant)
        <div class="v-card p-4 mt-4">
            <p class="text-sm text-gray-500">Your company registration is currently</p>
            <p class="text-lg font-semibold text-gray-900 capitalize">{{ $pendingTenant->status }}</p>
            <p class="text-xs text-gray-400 mt-1">We will notify you once it has been reviewed.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <div class="v-card p-4 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Register as PIC</h3>
                <p class="text-sm text-gray-500 mt-1">Submit subsidiary details and wait for approval from the holding administrator.</p>
            </div>
            <div class="mt-4">
                <a href="{{ route('tenants.register') }}" class="v-btn-primary inline-flex">Register Company</a>
            </div>
        </div>
        <div class="v-card p-4 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Join as User</h3>
                <p class="text-sm text-gray-500 mt-1">Ask your subsidiary admin to add you to their tenant. You will get access after being assigned.</p>
            </div>
            <div class="mt-4">
                <a href="{{ route('profile.edit') }}" class="v-btn-secondary inline-flex">Update Profile</a>
            </div>
        </div>
    </div>
</x-app-layout>

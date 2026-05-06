@section('title', 'Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-gray-900">Dashboard</h1>
    </x-slot>

    {{-- Welcome --}}
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Welcome back, {{ Auth::user()->name }}</h2>
        <p class="text-sm text-gray-500 mt-0.5">Here's what's happening with your projects.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="v-card p-4">
            <h3 class="text-sm font-semibold text-gray-900">Company Details</h3>
            <div class="mt-3 text-sm">
                <p class="text-gray-500 text-xs">Subsidiary</p>
                <p class="text-gray-900 font-medium">{{ $tenant?->company_name ?? 'Not assigned' }}</p>
            </div>
            <div class="mt-2 text-sm">
                <p class="text-gray-500 text-xs">PIC</p>
                <p class="text-gray-900 font-medium">{{ $tenant?->pic_name ?? '-' }}</p>
                <p class="text-gray-500 text-xs">{{ $tenant?->pic_email ?? '-' }}</p>
            </div>
            <div class="mt-3">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600 capitalize">{{ $tenant?->status ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="v-card p-4">
            <h3 class="text-sm font-semibold text-gray-900">Project Invitations</h3>
            <p class="text-sm text-gray-500 mt-1">Pending invitations for your account.</p>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-2xl font-semibold text-gray-900 tabular-nums">{{ $pendingInvitations }}</span>
                <a href="{{ route('invitations.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">View invitations</a>
            </div>
        </div>
    </div>

    
</x-app-layout>
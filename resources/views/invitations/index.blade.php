@section('title', 'Project Invitations')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-gray-900">Project Invitations</h1>
    </x-slot>

    <div class="v-card p-4">
        <h2 class="text-sm font-semibold text-gray-900">Pending Invitations</h2>
        <p class="text-xs text-gray-400 mt-1">Accept or decline to join projects.</p>

        <div class="mt-4 space-y-3">
            @forelse ($invitations as $invitation)
                <div class="border border-border rounded-sm p-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $invitation->project->title }}</p>
                        <p class="text-xs text-gray-400">Invited by {{ $invitation->inviter?->name ?? 'System' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('invitations.accept', $invitation) }}">
                            @csrf
                            @method('PATCH')
                            <button class="v-btn-primary text-[12px]" type="submit">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('invitations.decline', $invitation) }}">
                            @csrf
                            @method('PATCH')
                            <button class="v-btn-secondary text-[12px]" type="submit">Decline</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No pending invitations.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>

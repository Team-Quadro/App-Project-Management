@section('title', 'Tenant Approvals')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-ink">Tenant Approvals</h1>
    </x-slot>

    <div class="v-card p-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-ink">Registrations</h2>
                <p class="text-[13px] text-ink-subtle mt-0.5">Review and manage subsidiary registrations.</p>
            </div>
            <a href="{{ route('superadmin.tenants.create') }}" class="v-btn-primary text-[12px] gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Subsidiary
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-hairline text-ink-subtle text-[12px] uppercase tracking-wider">
                        <th class="py-3 px-4 font-medium">Company</th>
                        <th class="py-3 px-4 font-medium">PIC</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium">Est. Users</th>
                        <th class="py-3 px-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($tenants as $tenant)
                        <tr class="group hover:bg-surface-2/50 transition-colors cursor-pointer" onclick="window.location='{{ route('superadmin.tenants.show', $tenant) }}'">
                            <td class="py-3 px-4">
                                <div class="font-medium text-ink text-[14px]">{{ $tenant->company_name }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-[13px] text-ink">{{ $tenant->pic_name }}</div>
                                <div class="text-[12px] text-ink-muted mt-0.5">{{ $tenant->pic_email }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <x-badge :variant="$tenant->status" size="xs">{{ ucfirst($tenant->status) }}</x-badge>
                            </td>
                            <td class="py-3 px-4 tabular-nums text-[13px] text-ink-subtle">{{ $tenant->estimated_users ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-3 rounded transition-colors" title="View Details" @click.stop>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-3 rounded transition-colors" title="Edit" @click.stop>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('superadmin.tenants.destroy', $tenant) }}" class="inline-flex" onsubmit="return confirm('Are you sure you want to delete this tenant?')" @click.stop>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-ink-subtle hover:text-red-400 hover:bg-surface-3 rounded transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-8 px-4 text-center text-ink-subtle text-[13px]" colspan="5">No tenant registrations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tenants->links() }}
        </div>
    </div>
</x-app-layout>

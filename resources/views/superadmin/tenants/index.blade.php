@section('title', 'Tenant Approvals')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-gray-900">Tenant Approvals</h1>
    </x-slot>

    <div class="v-card p-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Registrations</h2>
                <p class="text-xs text-gray-400">Review and manage subsidiary registrations.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400">
                        <th class="py-2 pr-4">Company</th>
                        <th class="py-2 pr-4">PIC</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">Estimated Users</th>
                        <th class="py-2">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($tenants as $tenant)
                        <tr class="border-t border-border">
                            <td class="py-3 pr-4">
                                <div class="font-medium text-gray-900">{{ $tenant->company_name }}</div>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="text-gray-900">{{ $tenant->pic_name }}</div>
                                <div class="text-xs text-gray-400">{{ $tenant->pic_email }}</div>
                            </td>
                            <td class="py-3 pr-4 capitalize">{{ $tenant->status }}</td>
                            <td class="py-3 pr-4 tabular-nums">{{ $tenant->estimated_users ?? '-' }}</td>
                            <td class="py-3">
                                <div class="mb-2">
                                    <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">View details</a>
                                </div>
                                <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" class="flex flex-col gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex gap-2">
                                        <select name="status" class="v-input py-1">
                                            @foreach (\App\Models\Tenant::STATUSES as $status)
                                                <option value="{{ $status }}" @selected($tenant->status === $status)>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="v-btn-primary px-3 py-1">Update</button>
                                    </div>
                                    <input type="text" name="approval_notes" value="{{ old('approval_notes', $tenant->approval_notes) }}" class="v-input py-1" placeholder="Approval notes (optional)" />
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-border">
                            <td class="py-4 text-gray-400" colspan="5">No tenant registrations found.</td>
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

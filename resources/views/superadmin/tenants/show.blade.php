@section('title', 'Tenant Details')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-ink">Tenant Details</h1>
    </x-slot>

    <div class="mb-4 flex items-center gap-3">
        <a href="{{ route('superadmin.tenants.index') }}" class="text-[13px] font-medium text-primary hover:text-white transition-colors">← Back to tenants</a>
        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="text-[13px] font-medium text-ink-subtle hover:text-ink transition-colors">Edit subsidiary</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="v-card p-4 lg:col-span-2">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-ink">{{ $tenant->company_name }}</h2>
                </div>
                <x-badge :variant="$tenant->status" size="xs">{{ ucfirst($tenant->status) }}</x-badge>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-[13px]">
                <div>
                    <p class="text-[12px] text-ink-subtle">PIC</p>
                    <p class="text-ink font-medium">{{ $tenant->pic_name }}</p>
                    <p class="text-ink-muted text-[12px]">{{ $tenant->pic_email }}</p>
                </div>
                <div>
                    <p class="text-[12px] text-ink-subtle">Industry</p>
                    <p class="text-ink font-medium">{{ $tenant->industry ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[12px] text-ink-subtle">Estimated Users</p>
                    <p class="text-ink font-medium">{{ $tenant->estimated_users ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[12px] text-ink-subtle">PIC Phone</p>
                    <p class="text-ink font-medium">{{ $tenant->pic_phone ?? '—' }}</p>
                </div>
            </div>

            <div class="mt-5">
                <form method="POST" action="{{ route('superadmin.tenants.status', $tenant) }}" class="flex flex-col gap-2">
                    @csrf
                    @method('PATCH')
                    <div class="flex gap-2">
                        <select name="status" class="v-input py-1">
                            @foreach (\App\Models\Tenant::STATUSES as $status)
                                <option value="{{ $status }}" @selected($tenant->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="v-btn-primary px-3 py-1">Update Status</button>
                    </div>
                    <input type="text" name="approval_notes" value="{{ old('approval_notes', $tenant->approval_notes) }}" class="v-input py-1" placeholder="Approval notes (optional)" />
                </form>
            </div>
        </div>

        <div class="v-card p-4">
            <h3 class="text-sm font-semibold text-ink">Performance</h3>
            <div class="mt-3 space-y-2 text-[13px]">
                <div class="flex items-center justify-between">
                    <span class="text-ink-subtle">Projects</span>
                    <span class="font-medium text-ink tabular-nums">{{ $projectCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-ink-subtle">Users</span>
                    <span class="font-medium text-ink tabular-nums">{{ $userCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-ink-subtle">Tasks</span>
                    <span class="font-medium text-ink tabular-nums">{{ $totalTasks }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-ink-subtle">Completed Tasks</span>
                    <span class="font-medium text-ink tabular-nums">{{ $doneTasks }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-ink-subtle">Completion Rate</span>
                    <span class="font-medium text-ink tabular-nums">{{ number_format($completionRate, 1) }}%</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

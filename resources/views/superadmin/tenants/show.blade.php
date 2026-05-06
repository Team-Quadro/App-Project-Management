@section('title', 'Tenant Details')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-gray-900">Tenant Details</h1>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('superadmin.tenants.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">← Back to tenants</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="v-card p-4 lg:col-span-2">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $tenant->company_name }}</h2>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 capitalize">{{ $tenant->status }}</span>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-400">PIC</p>
                    <p class="text-gray-900 font-medium">{{ $tenant->pic_name }}</p>
                    <p class="text-gray-500 text-xs">{{ $tenant->pic_email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Industry</p>
                    <p class="text-gray-900 font-medium">{{ $tenant->industry ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Estimated Users</p>
                    <p class="text-gray-900 font-medium">{{ $tenant->estimated_users ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">PIC Phone</p>
                    <p class="text-gray-900 font-medium">{{ $tenant->pic_phone ?? '—' }}</p>
                </div>
            </div>

            <div class="mt-5">
                <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" class="flex flex-col gap-2">
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
            <h3 class="text-sm font-semibold text-gray-900">Performance</h3>
            <div class="mt-3 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Projects</span>
                    <span class="font-medium text-gray-900 tabular-nums">{{ $projectCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Users</span>
                    <span class="font-medium text-gray-900 tabular-nums">{{ $userCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Tasks</span>
                    <span class="font-medium text-gray-900 tabular-nums">{{ $totalTasks }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Completed Tasks</span>
                    <span class="font-medium text-gray-900 tabular-nums">{{ $doneTasks }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Completion Rate</span>
                    <span class="font-medium text-gray-900 tabular-nums">{{ number_format($completionRate, 1) }}%</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

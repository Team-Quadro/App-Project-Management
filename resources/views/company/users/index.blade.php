@section('title', 'Company Members')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-ink">Members</h1>
    </x-slot>

    <div x-data="{ showCreate: false, editingUser: null }" class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-ink">Team Members</h2>
                <p class="text-[13px] text-ink-subtle mt-0.5">Manage members of {{ $tenant?->company_name ?? 'your company' }}</p>
            </div>
            <button @click="showCreate = !showCreate" class="v-btn-primary text-[13px] gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Member
            </button>
        </div>

        {{-- Create Member Form --}}
        <div x-show="showCreate" x-cloak x-transition class="v-card p-5">
            <h3 class="text-[14px] font-semibold text-ink mb-3">New Member</h3>
            <form method="POST" action="{{ route('company.users.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="v-label">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="v-input text-[13px]" placeholder="Full name" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <label class="v-label">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="v-input text-[13px]" placeholder="email@example.com" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div>
                        <label class="v-label">Job Title</label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}" class="v-input text-[13px]" placeholder="e.g. Developer" />
                    </div>
                </div>
                <p class="text-[12px] text-ink-muted">Default password: <code class="text-ink-subtle bg-surface-3 px-1.5 py-0.5 rounded text-[11px]">password</code></p>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="v-btn-primary text-[13px]">Create Member</button>
                    <button type="button" @click="showCreate = false" class="v-btn-secondary text-[13px]">Cancel</button>
                </div>
            </form>
        </div>

        {{-- Members Table --}}
        <div class="v-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-hairline text-[11px] font-medium text-ink-subtle uppercase tracking-wider">
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">Job Title</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 w-20"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline/50">
                        @forelse ($members as $member)
                        <tr class="group hover:bg-surface-1/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-primary/20 text-primary flex items-center justify-center text-[11px] font-bold shrink-0">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-medium text-ink truncate">{{ $member->name }}</p>
                                        <p class="text-[11px] text-ink-muted truncate">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-[13px] text-ink-subtle">{{ $member->job_title ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $member->role === 'pic' ? 'bg-primary/15 text-primary' : 'bg-surface-3 text-ink-subtle' }}">
                                    {{ $member->role_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($member->is_active)
                                <span class="inline-flex items-center gap-1 text-[11px] text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Active
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 text-[11px] text-ink-muted">
                                    <span class="w-1.5 h-1.5 rounded-full bg-ink-muted"></span> Inactive
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($member->id !== auth()->id() && $member->role !== 'pic')
                                <div class="flex gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{-- Toggle active --}}
                                    <form method="POST" action="{{ route('company.users.update', $member) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="name" value="{{ $member->name }}">
                                        <input type="hidden" name="is_active" value="{{ $member->is_active ? '0' : '1' }}">
                                        <button type="submit" class="p-1.5 rounded transition-colors {{ $member->is_active ? 'text-ink-subtle hover:text-yellow-400 hover:bg-yellow-500/10' : 'text-ink-subtle hover:text-green-400 hover:bg-green-500/10' }}"
                                                title="{{ $member->is_active ? 'Deactivate' : 'Activate' }}">
                                            @if($member->is_active)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('company.users.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-ink-subtle hover:text-red-400 hover:bg-red-500/10 rounded transition-colors" title="Remove">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[13px] text-ink-subtle">No members yet. Add your first team member above.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pending Join Requests --}}
        @if($requests->count())
        <div class="v-card overflow-hidden">
            <div class="px-4 py-3 border-b border-hairline">
                <h3 class="text-[14px] font-semibold text-ink">Pending Join Requests</h3>
                <p class="text-[12px] text-ink-subtle mt-0.5">Users who want to join your subsidiary</p>
            </div>
            <div class="divide-y divide-hairline/50">
                @foreach ($requests as $request)
                <div class="flex items-center justify-between px-4 py-3 group hover:bg-surface-1/50 transition-colors">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-[11px] font-bold shrink-0">
                            {{ strtoupper(substr($request->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-[13px] font-medium text-ink">{{ $request->user->name }}</p>
                            <p class="text-[11px] text-ink-muted">{{ $request->user->email }} · {{ $request->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <form method="POST" action="{{ route('company.users.reject', $request) }}" title="Reject">
                            @csrf @method('PATCH')
                            <button class="p-1.5 text-ink-subtle hover:text-red-400 hover:bg-red-500/10 rounded transition-colors" type="submit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('company.users.approve', $request) }}" title="Approve">
                            @csrf @method('PATCH')
                            <button class="p-1.5 text-primary hover:text-white hover:bg-primary rounded transition-colors" type="submit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

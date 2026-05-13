@section('title', 'User Management')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-sm font-semibold text-ink">User Management</h1>
            <button class="v-btn-primary text-[12px] gap-1.5" type="button" x-data x-on:click="$dispatch('open-modal', 'create-user')">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add User
            </button>
        </div>
    </x-slot>

    @if ($errors->any())
        <x-alert type="error" class="mb-4">
            <div>
                <p class="font-medium">Please review the highlighted fields.</p>
                <ul class="mt-1 list-disc list-inside text-[12px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </x-alert>
    @endif

    <div class="v-card p-4">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-hairline text-ink-subtle text-[12px] uppercase tracking-wider">
                        <th class="py-3 px-4 font-medium">Name</th>
                        <th class="py-3 px-4 font-medium">Email</th>
                        <th class="py-3 px-4 font-medium">Role</th>
                        <th class="py-3 px-4 font-medium">Subsidiary</th>
                        <th class="py-3 px-4 font-medium">Active</th>
                        <th class="py-3 px-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @foreach ($users as $user)
                        <form id="user-{{ $user->id }}" method="POST" action="{{ route('superadmin.users.update', $user) }}">
                            @csrf
                            @method('PATCH')
                        </form>
                        <tr class="group hover:bg-surface-2/50 transition-colors">
                            <td class="py-2 px-4">
                                <input class="v-input py-1.5 text-[13px]" name="name" type="text" value="{{ $user->name }}" required form="user-{{ $user->id }}" />
                            </td>
                            <td class="py-2 px-4">
                                <input class="v-input py-1.5 text-[13px]" name="email" type="email" value="{{ $user->email }}" required form="user-{{ $user->id }}" />
                            </td>
                            <td class="py-2 px-4">
                                <select class="v-select py-1.5 text-[13px]" name="role" required form="user-{{ $user->id }}">
                                    <option value="superadmin" @selected($user->role === 'superadmin')>Superadmin</option>
                                    <option value="pic" @selected($user->role === 'pic')>PIC Company</option>
                                    <option value="member" @selected($user->role === 'member')>Member</option>
                                </select>
                            </td>
                            <td class="py-2 px-4">
                                <select class="v-select py-1.5 text-[13px]" name="tenant_id" form="user-{{ $user->id }}">
                                    <option value="">Not assigned</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" @selected($user->tenant_id === $tenant->id)>{{ $tenant->company_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-2 px-4">
                                <label class="inline-flex items-center gap-2 text-[13px] text-ink-subtle">
                                    <input type="checkbox" name="is_active" value="1" @checked($user->is_active) class="rounded-sm border-hairline text-primary focus:ring-primary/20 bg-canvas" form="user-{{ $user->id }}" />
                                    Active
                                </label>
                            </td>
                            <td class="py-2 px-4">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="submit" form="user-{{ $user->id }}" class="p-1.5 text-ink-subtle hover:text-green-500 hover:bg-surface-3 rounded transition-colors" title="Save changes">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" class="inline-flex" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-ink-subtle hover:text-red-400 hover:bg-surface-3 rounded transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>

    <x-modal name="create-user" :show="$errors->getBag('createUser')->any()">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Add User</h2>
                <button type="button" class="text-gray-400 hover:text-gray-600" x-on:click="$dispatch('close-modal', 'create-user')">×</button>
            </div>
            <form class="mt-4 space-y-3" method="POST" action="{{ route('superadmin.users.store') }}">
                @csrf
                <div>
                    <label class="v-label" for="create_name">Name</label>
                    <input class="v-input" id="create_name" name="name" type="text" value="{{ old('name') }}" required />
                    <x-input-error :messages="$errors->getBag('createUser')->get('name')" class="mt-1" />
                </div>
                <div>
                    <label class="v-label" for="create_email">Email</label>
                    <input class="v-input" id="create_email" name="email" type="email" value="{{ old('email') }}" required />
                    <x-input-error :messages="$errors->getBag('createUser')->get('email')" class="mt-1" />
                </div>
                <div>
                    <label class="v-label" for="create_password">Password</label>
                    <input class="v-input" id="create_password" name="password" type="password" required />
                    <x-input-error :messages="$errors->getBag('createUser')->get('password')" class="mt-1" />
                </div>
                <div>
                    <label class="v-label" for="create_role">Role</label>
                    <select class="v-select" id="create_role" name="role" required>
                        <option value="superadmin">Superadmin</option>
                        <option value="pic">PIC Company</option>
                        <option value="member" selected>Member</option>
                    </select>
                    <x-input-error :messages="$errors->getBag('createUser')->get('role')" class="mt-1" />
                </div>
                <div>
                    <label class="v-label" for="create_tenant_id">Subsidiary</label>
                    <select class="v-select" id="create_tenant_id" name="tenant_id">
                        <option value="">Not assigned</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->company_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->getBag('createUser')->get('tenant_id')" class="mt-1" />
                </div>
                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-[13px] text-ink-subtle">
                        <input type="checkbox" name="is_active" value="1" class="rounded-sm border-hairline text-primary focus:ring-primary/20 bg-canvas" />
                        Active User
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" class="v-btn-secondary text-[12px]" x-on:click="$dispatch('close-modal', 'create-user')">Cancel</button>
                        <button class="v-btn-primary text-[12px]" type="submit">Create User</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>

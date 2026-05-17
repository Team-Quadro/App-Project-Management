@section('title', 'Edit Subsidiary')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-ink">Edit Subsidiary</h1>
    </x-slot>

    <div class="max-w-xl">
        <div class="v-card p-5">
            <form method="POST" action="{{ route('superadmin.tenants.update', $tenant) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="v-label" for="company_name">Subsidiary Name</label>
                    <input class="v-input" id="company_name" name="company_name" type="text" value="{{ old('company_name', $tenant->company_name) }}" required />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                </div>
                <div>
                    <label class="v-label" for="industry">Industry</label>
                    <input class="v-input" id="industry" name="industry" type="text" value="{{ old('industry', $tenant->industry) }}" />
                    <x-input-error :messages="$errors->get('industry')" class="mt-1" />
                </div>
                <div>
                    <label class="v-label" for="pic_user_id">Assign PIC</label>
                    <select class="v-select" id="pic_user_id" name="pic_user_id">
                        <option value="">Select PIC</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected($tenant->pic_user_id === $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('pic_user_id')" class="mt-1" />
                </div>
                <div>
                    <label class="v-label">Assign Members</label>
                    <div class="border border-hairline bg-surface-1 rounded-md p-3 max-h-52 overflow-y-auto space-y-1 sidebar-scroll">
                        @foreach ($users as $user)
                            <label class="flex items-center gap-3 px-2 py-1.5 rounded-md hover:bg-surface-2 transition-colors duration-100 cursor-pointer text-[13px]">
                                <input type="checkbox" name="member_ids[]" value="{{ $user->id }}" class="rounded border-hairline bg-canvas text-primary focus:ring-primary/50 w-4 h-4"
                                    @checked(in_array($user->id, $memberIds, true)) />
                                <span class="text-ink">{{ $user->name }} <span class="text-ink-muted text-[11px] ml-1">({{ $user->email }})</span></span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('member_ids')" class="mt-1" />
                </div>

                <div class="flex items-center gap-2 pt-4 border-t border-hairline">
                    <button type="submit" class="v-btn-primary text-[13px]">Save Changes</button>
                    <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="v-btn-secondary text-[13px]">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

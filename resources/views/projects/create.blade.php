@section('title', 'New Project')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1.5 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-ink-subtle hover:text-ink transition-colors duration-100">Projects</a>
            <span class="text-hairline-strong">/</span>
            <span class="font-medium text-ink">New Project</span>
        </div>
    </x-slot>

    <div class="max-w-xl">
        <div class="v-card p-5">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="title" class="v-label">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus class="v-input" placeholder="Project title" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="mb-4">
                    <label for="description" class="v-label">Description</label>
                    <textarea id="description" name="description" rows="3" class="v-input resize-none" placeholder="Describe the project...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="status" class="v-label">Status</label>
                        <select id="status" name="status" class="v-select">
                            @foreach (\App\Models\Project::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', 'active')===$status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                    <div>
                        <label for="deadline" class="v-label">Deadline</label>
                        <input type="date" id="deadline" name="deadline" value="{{ old('deadline') }}" class="v-input" />
                        <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
                    </div>
                </div>

                {{-- Team Members (Checkbox) --}}
                <div class="mb-5" x-data="{ search: '' }">
                    <label class="v-label">Team Members</label>
                    <div class="border border-hairline rounded-md bg-surface-1 overflow-hidden">
                        @if($tenantUsers->count() > 5)
                        <div class="px-3 pt-3">
                            <input type="text" x-model="search" placeholder="Search members..." class="v-input !py-1.5 text-[13px] w-full bg-canvas border-hairline" />
                        </div>
                        @endif
                        <div class="max-h-48 overflow-y-auto sidebar-scroll p-3 space-y-1.5">
                            @forelse ($tenantUsers as $u)
                            <label class="flex items-center gap-3 py-1.5 px-2 rounded-md hover:bg-surface-2 cursor-pointer transition-colors text-[13px]"
                                   x-show="!search || '{{ strtolower($u->name . ' ' . $u->email) }}'.includes(search.toLowerCase())">
                                <input type="checkbox" name="member_ids[]" value="{{ $u->id }}"
                                       {{ in_array($u->id, old('member_ids', [])) ? 'checked' : '' }}
                                       class="rounded border-hairline bg-canvas text-primary focus:ring-primary/50 w-4 h-4" />
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <div class="w-6 h-6 rounded-full bg-surface-3 flex items-center justify-center text-[10px] font-bold text-ink-muted shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-ink font-medium truncate">{{ $u->name }}</p>
                                        <p class="text-ink-muted text-[11px] truncate">{{ $u->email }}{{ $u->job_title ? ' · ' . $u->job_title : '' }}</p>
                                    </div>
                                </div>
                            </label>
                            @empty
                            <p class="text-[13px] text-ink-subtle text-center py-3">No team members in your subsidiary yet.</p>
                            @endforelse
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('member_ids')" class="mt-1" />
                </div>

                <div class="flex items-center gap-2 pt-4 border-t border-hairline mt-2">
                    <button type="submit" class="v-btn-primary text-[13px]">Create Project</button>
                    <a href="{{ route('projects.index') }}" class="v-btn-secondary text-[13px]">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
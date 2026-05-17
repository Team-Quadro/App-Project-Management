@section('title', 'Edit: ' . $project->title)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1.5 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-ink-subtle hover:text-ink transition-colors duration-100">Proyek</a>
            <span class="text-hairline-strong">/</span>
            <a href="{{ route('projects.show', $project) }}" class="text-ink-subtle hover:text-ink transition-colors duration-100">{{ $project->title }}</a>
            <span class="text-hairline-strong">/</span>
            <span class="font-medium text-ink">Edit</span>
        </div>
    </x-slot>

    <div class="max-w-xl">
        <div class="v-card p-5">
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="title" class="v-label">Judul <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $project->title) }}" required autofocus class="v-input" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="mb-4">
                    <label for="description" class="v-label">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" class="v-input resize-none">{{ old('description', $project->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="status" class="v-label">Status</label>
                        <select id="status" name="status" class="v-select">
                            @foreach (\App\Models\Project::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', $project->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                    <div>
                        <label for="deadline" class="v-label">Tenggat Waktu</label>
                        <input type="date" id="deadline" name="deadline" value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}" class="v-input" />
                        <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
                    </div>
                </div>

                {{-- Team Members (Checkbox) --}}
                @php $currentMemberIds = old('member_ids', $project->members->pluck('id')->toArray()); @endphp
                <div class="mb-5" x-data="{ search: '' }">
                    <label class="v-label">Anggota Tim</label>
                    <div class="border border-hairline rounded-md bg-surface-1 overflow-hidden">
                        @if($tenantUsers->count() > 5)
                        <div class="px-3 pt-3">
                            <input type="text" x-model="search" placeholder="Cari anggota..." class="v-input !py-1.5 text-[13px] w-full bg-canvas border-hairline" />
                        </div>
                        @endif
                        <div class="max-h-48 overflow-y-auto sidebar-scroll p-3 space-y-1.5">
                            @forelse ($tenantUsers as $u)
                            <label class="flex items-center gap-3 py-1.5 px-2 rounded-md hover:bg-surface-2 cursor-pointer transition-colors text-[13px]"
                                   x-show="!search || '{{ strtolower($u->name . ' ' . $u->email) }}'.includes(search.toLowerCase())">
                                <input type="checkbox" name="member_ids[]" value="{{ $u->id }}"
                                       {{ in_array($u->id, $currentMemberIds) ? 'checked' : '' }}
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
                            <p class="text-[13px] text-ink-subtle text-center py-3">Belum ada anggota tim di perusahaan Anda.</p>
                            @endforelse
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('member_ids')" class="mt-1" />
                </div>

                <div class="flex items-center gap-2 pt-4 border-t border-hairline">
                    <button type="submit" class="v-btn-primary text-[13px]">Perbarui Proyek</button>
                    <a href="{{ route('projects.show', $project) }}" class="v-btn-secondary text-[13px]">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
@section('title', 'Edit: ' . $project->title)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1.5 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-brand-600 transition-colors duration-100">Projects</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('projects.show', $project) }}" class="text-gray-500 hover:text-brand-600 transition-colors duration-100">{{ $project->title }}</a>
            <span class="text-gray-300">/</span>
            <span class="font-medium text-gray-900">Edit</span>
        </div>
    </x-slot>

    <div class="max-w-xl">
        <div class="v-card p-5">
            <form method="POST" action="{{ route('projects.update', $project) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="title" class="v-label">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $project->title) }}" required autofocus class="v-input" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="mb-4">
                    <label for="description" class="v-label">Description</label>
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
                        <label for="deadline" class="v-label">Deadline</label>
                        <input type="date" id="deadline" name="deadline" value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}" class="v-input" />
                        <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
                    </div>
                </div>

                @php $currentMembers = $project->members->pluck('id')->toArray(); @endphp
                <div class="mb-5">
                    <label class="v-label">Team Members</label>
                    <div class="border border-border rounded-[4px] p-3 max-h-44 overflow-y-auto space-y-1">
                        @foreach ($users as $user)
                        <label class="flex items-center gap-2 px-2 py-1.5 rounded-[3px] hover:bg-gray-50 transition-colors duration-100 cursor-pointer">
                            <input type="checkbox" name="members[]" value="{{ $user->id }}" {{ in_array($user->id, old('members', $currentMembers)) ? 'checked' : '' }}
                            class="rounded-[3px] border-border text-brand-600 focus:ring-brand-500/20" />
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-5 h-5 rounded-full bg-brand-100 text-brand-600 text-[9px] font-medium flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-[13px] text-gray-900 truncate">{{ $user->name }}</span>
                                <span class="text-[11px] text-gray-400 truncate">{{ $user->email }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-4 border-t border-border">
                    <button type="submit" class="v-btn-primary text-[13px]">Update Project</button>
                    <a href="{{ route('projects.show', $project) }}" class="v-btn-secondary text-[13px]">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
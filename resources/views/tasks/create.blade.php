@section('title', 'Add Task')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1.5 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-brand-600 transition-colors duration-100">Projects</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('projects.show', $project) }}" class="text-gray-500 hover:text-brand-600 transition-colors duration-100">{{ $project->title }}</a>
            <span class="text-gray-300">/</span>
            <span class="font-medium text-gray-900">Add Task</span>
        </div>
    </x-slot>

    <div class="max-w-xl">
        <div class="v-card p-5">
            <form method="POST" action="{{ route('projects.tasks.store', $project) }}">
                @csrf

                <div class="mb-4">
                    <label for="title" class="v-label">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus class="v-input" placeholder="What needs to be done?" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="mb-4">
                    <label for="description" class="v-label">Description</label>
                    <textarea id="description" name="description" rows="3" class="v-input resize-none" placeholder="Add more details...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="status" class="v-label">Status</label>
                        <select id="status" name="status" class="v-select">
                            @foreach (\App\Models\Task::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', 'todo')===$status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                    <div>
                        <label for="priority" class="v-label">Priority</label>
                        <select id="priority" name="priority" class="v-select">
                            @foreach (\App\Models\Task::PRIORITIES as $priority)
                            <option value="{{ $priority }}" {{ old('priority', 'medium')===$priority ? 'selected' : '' }}>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('priority')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="assigned_to" class="v-label">Assign To</label>
                        <select id="assigned_to" name="assigned_to" class="v-select">
                            <option value="">Unassigned</option>
                            @foreach ($projectUsers as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to')==$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('assigned_to')" class="mt-1" />
                    </div>
                    <div>
                        <label for="deadline" class="v-label">Deadline</label>
                        <input type="date" id="deadline" name="deadline" value="{{ old('deadline') }}" class="v-input" />
                        <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-4 border-t border-border">
                    <button type="submit" class="v-btn-primary text-[13px]">Create Task</button>
                    <a href="{{ route('projects.show', $project) }}" class="v-btn-secondary text-[13px]">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
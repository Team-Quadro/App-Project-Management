@section('title', 'New Project')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1.5 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-brand-600 transition-colors duration-100">Projects</a>
            <span class="text-gray-300">/</span>
            <span class="font-medium text-gray-900">New Project</span>
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
                            <option value="{{ $status }}" {{ old('status', 'active' )===$status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
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

                <div class="mb-5" x-data="emailPicker({ initial: @json(old('member_emails', [])) })">
                    <label class="v-label">Team Members (Email)</label>
                    <div class="border border-border rounded-sm p-3">
                        <div class="flex flex-wrap gap-2 mb-2">
                            <template x-for="(email, index) in emails" :key="email">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-gray-100 text-xs text-gray-700">
                                    <span x-text="email"></span>
                                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="remove(index)">×</button>
                                </span>
                            </template>
                        </div>
                           <input type="text" x-model="input" @keydown.enter.prevent="add()" @keydown.comma.prevent="add()" @blur="add()"
                               class="v-input" placeholder="Type email and press Enter" />
                        <template x-for="email in emails" :key="email">
                            <input type="hidden" name="member_emails[]" :value="email" />
                        </template>
                    </div>
                    <x-input-error :messages="$errors->get('member_emails')" class="mt-1" />
                </div>

                <div class="flex items-center gap-2 pt-4 border-t border-border">
                    <button type="submit" class="v-btn-primary text-[13px]">Create Project</button>
                    <a href="{{ route('projects.index') }}" class="v-btn-secondary text-[13px]">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    function emailPicker({ initial }) {
        return {
            emails: Array.isArray(initial) ? initial : [],
            input: '',
            add() {
                const value = this.input.trim().replace(/,$/, '').toLowerCase();
                if (!value) return;
                if (!this.emails.includes(value)) {
                    this.emails.push(value);
                }
                this.input = '';
            },
            remove(index) {
                this.emails.splice(index, 1);
            }
        };
    }
</script>
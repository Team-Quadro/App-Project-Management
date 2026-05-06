<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-gray-900">Register Company</h1>
    </x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <p class="text-sm text-gray-500 mb-5">Submit your company registration for review by the holding administrator.</p>

    <form method="POST" action="{{ route('tenants.store') }}" class="max-w-xl">
        @csrf

        <div class="v-card p-3 mb-4">
            <p class="text-xs text-gray-400">PIC Account</p>
            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
        </div>

        <div>
            <label for="company_name" class="v-label">Subsidiary Company Name</label>
            <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" required class="v-input" placeholder="Subsidiary Company" />
            <x-input-error :messages="$errors->get('company_name')" class="mt-1.5" />
        </div>

        <div class="mt-4">
            <label for="industry" class="v-label">Industry</label>
            <input id="industry" type="text" name="industry" value="{{ old('industry') }}" class="v-input" placeholder="Industry" />
            <x-input-error :messages="$errors->get('industry')" class="mt-1.5" />
        </div>

        <div class="mt-4">
            <label for="pic_phone" class="v-label">PIC Phone</label>
            <input id="pic_phone" type="text" name="pic_phone" value="{{ old('pic_phone') }}" class="v-input" placeholder="+62 ..." />
            <x-input-error :messages="$errors->get('pic_phone')" class="mt-1.5" />
        </div>

        <div class="mt-4">
            <label for="pic_job_title" class="v-label">PIC Job Title</label>
            <input id="pic_job_title" type="text" name="pic_job_title" value="{{ old('pic_job_title') }}" class="v-input" placeholder="Company Admin" />
            <x-input-error :messages="$errors->get('pic_job_title')" class="mt-1.5" />
        </div>

        <div class="mt-4">
            <label for="estimated_users" class="v-label">Estimated Users</label>
            <input id="estimated_users" type="number" min="1" name="estimated_users" value="{{ old('estimated_users') }}" class="v-input" placeholder="e.g. 25" />
            <x-input-error :messages="$errors->get('estimated_users')" class="mt-1.5" />
        </div>

        <button type="submit" class="v-btn-primary w-full mt-5">
            Submit Registration
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
            Not ready to register a company?
            <a href="{{ route('onboarding.index') }}" class="text-brand-600 font-medium hover:text-brand-700">Back to options</a>
        </p>
    </form>
</x-app-layout>

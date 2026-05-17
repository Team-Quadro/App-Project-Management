@section('title', 'Register Company')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[13px]">
            <a href="{{ route('onboarding.index') }}" class="text-ink-subtle hover:text-ink transition-colors">Get Started</a>
            <span class="text-hairline-strong">/</span>
            <span class="font-medium text-ink">Register Company</span>
        </div>
    </x-slot>

    <div class="max-w-xl">
        <div class="mb-7">
            <h2 class="text-2xl font-bold text-ink tracking-tight">Register your subsidiary</h2>
            <p class="text-[14px] text-ink-subtle mt-1.5">Submit your company details for review by the holding administrator. You'll be notified once approved.</p>
        </div>

        <x-auth-session-status class="mb-5" :status="session('status')" />

        {{-- PIC Info Banner --}}
        <div class="v-card p-4 mb-6 flex items-center gap-3 border-primary/25 bg-primary/5">
            <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-[13px] font-semibold text-ink">{{ auth()->user()->name }}</p>
                <p class="text-[12px] text-ink-subtle">{{ auth()->user()->email }} · Registering as PIC</p>
            </div>
        </div>

        <form method="POST" action="{{ route('tenants.store') }}" class="space-y-5">
            @csrf

            <div class="v-card p-5 space-y-4">
                <h3 class="text-[11px] font-bold text-ink-subtle uppercase tracking-widest">Company Information</h3>

                <div>
                    <label for="company_name" class="v-label">Company name <span class="text-red-400">*</span></label>
                    <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" required
                        class="v-input" placeholder="PT. Example Indonesia" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-1.5" />
                </div>

                <div>
                    <label for="industry" class="v-label">Industry</label>
                    <input id="industry" type="text" name="industry" value="{{ old('industry') }}"
                        class="v-input" placeholder="e.g. Technology, Finance, Healthcare" />
                    <x-input-error :messages="$errors->get('industry')" class="mt-1.5" />
                </div>

                <div>
                    <label for="estimated_users" class="v-label">Estimated team size</label>
                    <input id="estimated_users" type="number" min="1" name="estimated_users" value="{{ old('estimated_users') }}"
                        class="v-input" placeholder="e.g. 25" />
                    <x-input-error :messages="$errors->get('estimated_users')" class="mt-1.5" />
                </div>
            </div>

            <div class="v-card p-5 space-y-4">
                <h3 class="text-[11px] font-bold text-ink-subtle uppercase tracking-widest">Your Contact Details</h3>

                <div>
                    <label for="pic_phone" class="v-label">Phone number</label>
                    <input id="pic_phone" type="text" name="pic_phone" value="{{ old('pic_phone') }}"
                        class="v-input" placeholder="+62 812 3456 7890" />
                    <x-input-error :messages="$errors->get('pic_phone')" class="mt-1.5" />
                </div>

                <div>
                    <label for="pic_job_title" class="v-label">Job title</label>
                    <input id="pic_job_title" type="text" name="pic_job_title" value="{{ old('pic_job_title') }}"
                        class="v-input" placeholder="e.g. Company Director, Manager" />
                    <x-input-error :messages="$errors->get('pic_job_title')" class="mt-1.5" />
                </div>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <button type="submit" class="v-btn-primary py-2.5 px-6">
                    Submit Registration
                </button>
                <a href="{{ route('onboarding.index') }}" class="v-btn-secondary py-2.5 px-5">
                    Back
                </a>
            </div>

            <p class="text-[12px] text-ink-muted pt-1">
                Your registration will be reviewed by the holding administrator. This usually takes 1–2 business days.
            </p>
        </form>
    </div>
</x-app-layout>

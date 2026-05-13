<x-guest-layout>
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="mb-7">
        <h2 class="text-2xl font-bold text-ink tracking-tight mb-1">Reset your password</h2>
        <p class="text-[14px] text-ink-subtle">We'll send a reset link to your email address.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="v-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="v-input" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <button type="submit" class="v-btn-primary w-full py-2.5">
            Send reset link
        </button>

        <p class="text-center text-[13px] text-ink-subtle pt-1">
            Remembered it?
            <a href="{{ route('login') }}" class="text-primary font-medium hover:text-primary-hover transition-colors">Back to sign in</a>
        </p>
    </form>
</x-guest-layout>

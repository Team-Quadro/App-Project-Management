<x-guest-layout>
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="mb-7">
        <h2 class="text-2xl font-bold text-ink tracking-tight mb-1">Welcome back</h2>
        <p class="text-[14px] text-ink-subtle">Sign in to your workspace.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="v-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="v-input" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="v-label !mb-0">Password</label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-[12px] text-primary hover:text-primary-hover transition-colors">
                    Forgot password?
                </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="v-input" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember"
                class="w-4 h-4 rounded border-hairline-strong bg-surface-1 text-primary focus:ring-primary/20">
            <label for="remember_me" class="text-[13px] text-ink-subtle">Keep me signed in</label>
        </div>

        <button type="submit" class="v-btn-primary w-full py-2.5 mt-2">
            Sign in
        </button>

        @if (Route::has('register'))
        <p class="text-center text-[13px] text-ink-subtle pt-1">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-primary font-medium hover:text-primary-hover transition-colors">Create one</a>
        </p>
        @endif
    </form>
</x-guest-layout>

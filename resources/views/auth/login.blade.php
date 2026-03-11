<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-lg font-semibold text-gray-900 mb-1">Sign in</h2>
    <p class="text-sm text-gray-500 mb-5">Enter your credentials to continue.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="v-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="v-input" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="mt-4">
            <label for="password" class="v-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="v-input" placeholder="Password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded-[3px] border-border text-brand-600 focus:ring-brand-500/20" name="remember">
                <span class="ms-2 text-sm text-gray-500">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-brand-600 hover:text-brand-700 transition-colors" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="v-btn-primary w-full mt-5">
            Sign in
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-brand-600 font-medium hover:text-brand-700">Sign up</a>
        </p>
    </form>
</x-guest-layout>

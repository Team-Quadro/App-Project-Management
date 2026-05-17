<x-guest-layout>
    <div class="mb-7">
        <h2 class="text-2xl font-bold text-ink tracking-tight mb-1">Create your account</h2>
        <p class="text-[14px] text-ink-subtle">Get started with ProjectHub for free.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="v-label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="v-input" placeholder="Your full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div>
            <label for="email" class="v-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="v-input" placeholder="you@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="v-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="v-input" placeholder="Min. 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="password_confirmation" class="v-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="v-input" placeholder="Repeat password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit" class="v-btn-primary w-full py-2.5 mt-2">
            Create account
        </button>

        <p class="text-center text-[13px] text-ink-subtle pt-1">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary font-medium hover:text-primary-hover transition-colors">Sign in</a>
        </p>
    </form>
</x-guest-layout>

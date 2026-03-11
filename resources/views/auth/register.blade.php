<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 mb-1">Create account</h2>
    <p class="text-sm text-gray-500 mb-5">Get started with ProjectHub.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label for="name" class="v-label">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="v-input" placeholder="Your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div class="mt-4">
            <label for="email" class="v-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="v-input" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="mt-4">
            <label for="password" class="v-label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="v-input" placeholder="Min. 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="v-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="v-input" placeholder="Repeat password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <button type="submit" class="v-btn-primary w-full mt-5">
            Create account
        </button>

        <p class="text-center text-sm text-gray-500 mt-4">
            Already have an account?
            <a href="{{ route('login') }}" class="text-brand-600 font-medium hover:text-brand-700">Sign in</a>
        </p>
    </form>
</x-guest-layout>

<x-guest-layout>
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="mb-7">
        <h2 class="text-2xl font-bold text-ink tracking-tight mb-1">
            Welcome back
        </h2>
        <p class="text-[14px] text-ink-subtle">
            Sign in to your workspace.
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="v-label">
                Email address
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="v-input"
                placeholder="you@company.com"
            />

            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="v-label !mb-0">
                    Password
                </label>
            </div>

            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="v-input pr-10"
                    placeholder="••••••••"
                />

                <button
                    type="button"
                    onclick="togglePassword()"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-ink-subtle hover:text-ink transition-colors"
                >
                    {{-- Eye Open --}}
                    <svg
                        id="eye-open"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0s-3.5 7-9 7-9-7-9-7 3.5-7 9-7 9 7 9 7z"
                        />
                    </svg>

                    {{-- Eye Closed --}}
                    <svg
                        id="eye-closed"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 hidden"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 3l18 18M10.477 10.476a3 3 0 004.242 4.242M9.88 5.09A9.953 9.953 0 0112 5c5.523 0 10 7 10 7a19.902 19.902 0 01-2.042 2.94M6.228 6.228A19.46 19.46 0 002 12s3.5 7 10 7a9.95 9.95 0 005.772-1.772"
                        />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="v-btn-primary w-full py-2.5 mt-2"
        >
            Sign in
        </button>

        {{-- Register --}}
        @if (Route::has('register'))
            <p class="text-center text-[13px] text-ink-subtle pt-1">
                Don't have an account?

                <a
                    href="{{ route('register') }}"
                    class="text-primary font-medium hover:text-primary-hover transition-colors"
                >
                    Create one
                </a>
            </p>
        @endif
    </form>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';

                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';

                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>
<section>
    <header>
        <h2 class="text-[14px] font-medium text-ink">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-[12px] text-ink-subtle">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="v-label">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="v-input" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="v-label">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="v-input" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-hairline">
            <button type="submit" class="v-btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-[12px] text-ink-subtle"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

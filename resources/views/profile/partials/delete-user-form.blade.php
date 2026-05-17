<section class="space-y-4">
    <header>
        <h2 class="text-[14px] font-medium text-ink">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-[12px] text-ink-subtle">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" class="v-btn-danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5">
            @csrf
            @method('delete')

            <h2 class="text-[14px] font-medium text-ink">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-[12px] text-ink-subtle">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-4">
                <label for="password" class="sr-only">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="v-input w-full"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" class="v-btn-secondary" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="v-btn-danger ms-0">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

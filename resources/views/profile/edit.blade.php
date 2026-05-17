<x-app-layout>
    <x-slot name="header">
        <span class="text-[13px] font-medium text-ink">Profile</span>
    </x-slot>

    <div class="w-full space-y-5">
        <div class="v-card p-5">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="v-card p-5">
            @include('profile.partials.update-password-form')
        </div>

        <div class="v-card p-5">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>

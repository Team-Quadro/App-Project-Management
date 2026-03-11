<button {{ $attributes->merge(['type' => 'button', 'class' => 'v-btn-secondary disabled:opacity-25']) }}>
    {{ $slot }}
</button>

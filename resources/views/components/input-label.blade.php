@props(['value'])

<label {{ $attributes->merge(['class' => 'v-label']) }}>
    {{ $value ?? $slot }}
</label>

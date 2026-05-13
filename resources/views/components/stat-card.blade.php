@props(['label', 'value', 'icon' => null, 'trend' => null, 'color' => 'primary'])

<div class="v-card p-4">
    <p class="text-[13px] text-ink-subtle font-medium">{{ $label }}</p>
    <p class="text-3xl font-semibold text-ink mt-2 tabular-nums {{ $attributes->get('class') }}">{{ $value }}</p>
    @if ($trend)
    <p class="text-[12px] text-ink-muted mt-1">{{ $trend }}</p>
    @endif
</div>
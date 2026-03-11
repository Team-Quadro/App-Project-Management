@props(['label', 'value', 'icon' => null, 'trend' => null, 'color' => 'brand'])

<div class="v-card p-4">
    <p class="text-[12px] text-gray-500 font-medium">{{ $label }}</p>
    <p class="text-2xl font-semibold text-gray-900 mt-1 tabular-nums">{{ $value }}</p>
    @if ($trend)
    <p class="text-[12px] text-gray-400 mt-1">{{ $trend }}</p>
    @endif
</div>
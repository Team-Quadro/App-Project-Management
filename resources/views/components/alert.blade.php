@props(['type' => 'info'])

@php
$styles = [
    'success' => 'bg-white border-green-200 text-green-800',
    'error' => 'bg-white border-red-200 text-red-800',
    'warning' => 'bg-white border-yellow-200 text-yellow-800',
    'info' => 'bg-white border-border text-gray-800',
];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 px-3 py-2.5 rounded-[4px] border text-[13px] ' . ($styles[$type] ?? $styles['info'])]) }}>
    {{ $slot }}
</div>
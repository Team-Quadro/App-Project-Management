@props(['variant' => 'default', 'size' => 'sm'])

@php
$colors = [
    'active' => 'bg-brand-50 text-brand-700 border border-brand-200',
    'completed' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    'archived' => 'bg-gray-100 text-gray-500 border border-gray-200',
    'todo' => 'bg-gray-100 text-gray-600 border border-gray-200',
    'in_progress' => 'bg-blue-50 text-blue-700 border border-blue-200',
    'done' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    'low' => 'bg-gray-100 text-gray-600 border border-gray-200',
    'medium' => 'bg-amber-50 text-amber-700 border border-amber-200',
    'high' => 'bg-red-50 text-red-700 border border-red-200',
    'default' => 'bg-gray-100 text-gray-600 border border-gray-200',
];

$sizes = [
    'xs' => 'text-[10px] px-1.5 py-0.5',
    'sm' => 'text-[11px] px-2 py-0.5',
    'md' => 'text-[12px] px-2.5 py-0.5',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center font-medium rounded-[3px] ' .
    ($colors[$variant] ?? $colors['default']) . ' ' . ($sizes[$size] ?? $sizes['sm'])]) }}>
    {{ $slot }}
</span>
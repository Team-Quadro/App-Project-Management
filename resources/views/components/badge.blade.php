@props(['variant' => 'neutral', 'size' => 'sm'])

@php
    $variants = [
        'active' => 'bg-success/15 text-success border border-success/20',
        'completed' => 'bg-success/15 text-success border border-success/20',
        'archived' => 'bg-white/5 text-text-tertiary border border-white/5',
        'todo' => 'bg-white/5 text-text-secondary border border-white/5',
        'in_progress' => 'bg-accent-violet/15 text-accent-violet border border-accent-violet/20',
        'done' => 'bg-success/15 text-success border border-success/20',
        'low' => 'bg-white/5 text-text-secondary border border-white/5',
        'medium' => 'bg-accent-violet/15 text-accent-violet border border-accent-violet/20',
        'high' => 'bg-red-500/15 text-red-400 border border-red-500/20',
        'success' => 'bg-success text-white',
        'neutral' => 'bg-transparent text-text-secondary border border-[#23252a]',
        'subtle' => 'bg-white/5 text-text-primary border border-white/5',
    ];

    $sizes = [
        'xs' => 'text-[10px] px-1.5 py-0.5 rounded-[2px]',
        'sm' => 'text-[11px] px-2 py-0.5 rounded-[3px]',
        'md' => 'text-[12px] px-2.5 py-0.5 rounded-[4px]',
    ];

    $variantClasses = $variants[$variant] ?? $variants['neutral'];
    $sizeClasses = $sizes[$size] ?? $sizes['sm'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-[510] {$variantClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
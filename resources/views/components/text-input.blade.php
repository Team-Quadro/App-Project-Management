@props(['disabled' => false])

<input 
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'w-full px-3 py-2 text-[13px] font-[510] bg-white/5 border border-white/10 rounded-md text-text-secondary placeholder:text-text-tertiary transition-colors duration-150 hover:border-white/20 focus:outline-none focus:border-white/20 focus:shadow-[0_0_0_1px_rgba(255,255,255,0.12),0_4px_12px_rgba(0,0,0,0.3)] disabled:opacity-50 disabled:cursor-not-allowed']) }}
>

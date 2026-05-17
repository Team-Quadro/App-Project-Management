@props(['disabled' => false])

<input 
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'v-input disabled:opacity-50 disabled:cursor-not-allowed']) }}
>

@props(['title' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'v-card']) }}>
    @if ($title)
    <div class="px-4 py-3 border-b border-border">
        <h3 class="text-[13px] font-semibold text-gray-900">{{ $title }}</h3>
    </div>
    @endif
    <div class="{{ $padding ? 'p-4' : '' }}">
        {{ $slot }}
    </div>
</div>
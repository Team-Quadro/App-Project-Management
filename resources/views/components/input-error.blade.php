@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-[12px] text-red-600 space-y-0.5']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif

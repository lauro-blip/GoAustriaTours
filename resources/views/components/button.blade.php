@props(['href' => null, 'variant' => 'primary', 'type' => 'button'])
@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-semibold transition focus-visible:outline-offset-2';
    $styles = [
        'primary' => 'bg-cta text-white hover:bg-cta-dark',
        'secondary' => 'bg-brand text-white hover:bg-brand-dark',
        'outline' => 'border-2 border-brand text-brand hover:bg-brand hover:text-white',
        'light' => 'bg-white text-brand hover:bg-brand-soft',
    ][$variant] ?? '';
    $class = "$base $styles";
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif

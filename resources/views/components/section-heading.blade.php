@props(['eyebrow' => null, 'title', 'subtitle' => null, 'center' => true, 'tag' => 'h2', 'light' => false])
<div {{ $attributes->merge(['class' => 'max-w-3xl mb-10 '.($center ? 'mx-auto text-center' : '')]) }}>
    @if($eyebrow)<p class="text-xs font-semibold uppercase tracking-[0.2em] {{ $light ? 'text-accent' : 'text-accent-text' }}">{{ $eyebrow }}</p>@endif
    <{{ $tag }} class="mt-2 text-4xl sm:text-5xl {{ $light ? '!text-white' : '' }}">{{ $title }}</{{ $tag }}>
    @if($subtitle)<p class="mt-4 text-lg {{ $light ? 'text-white/80' : 'text-muted' }}">{{ $subtitle }}</p>@endif
</div>

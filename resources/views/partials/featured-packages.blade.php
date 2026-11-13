{{-- Featured packages grid + prev/next/numbered pagination. Rendered inline on the home page and returned alone by the ajax endpoint (packages.featured). --}}
@php
    $current = $packages->currentPage();
    $last = $packages->lastPage();
    $url = fn (int $n) => route('home', $n > 1 ? ['featured_page' => $n] : []).'#featured';
    $btn = 'inline-flex size-10 items-center justify-center rounded-full border text-sm font-semibold transition';
@endphp
<div data-featured-grid class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($packages as $p)<x-package-card :package="$p" />@endforeach
</div>

@if($last > 1)
<nav class="mt-10 flex items-center justify-center gap-2" aria-label="Featured packages pages" data-featured-nav>
    @if($current > 1)
        <a href="{{ $url($current - 1) }}" data-page="{{ $current - 1 }}" rel="prev" aria-label="Previous page" class="{{ $btn }} border-line bg-white text-brand hover:bg-brand hover:text-white"><x-icon name="chevron" class="size-5 rotate-90" /></a>
    @else
        <span aria-hidden="true" class="{{ $btn }} border-line bg-white/50 text-muted/40"><x-icon name="chevron" class="size-5 rotate-90" /></span>
    @endif

    @foreach(range(1, $last) as $n)
        @if($n === $current)
            <span aria-current="page" class="{{ $btn }} border-brand bg-brand text-white">{{ $n }}</span>
        @else
            <a href="{{ $url($n) }}" data-page="{{ $n }}" aria-label="Page {{ $n }}" class="{{ $btn }} border-line bg-white text-brand hover:bg-brand-soft">{{ $n }}</a>
        @endif
    @endforeach

    @if($current < $last)
        <a href="{{ $url($current + 1) }}" data-page="{{ $current + 1 }}" rel="next" aria-label="Next page" class="{{ $btn }} border-line bg-white text-brand hover:bg-brand hover:text-white"><x-icon name="chevron" class="size-5 -rotate-90" /></a>
    @else
        <span aria-hidden="true" class="{{ $btn }} border-line bg-white/50 text-muted/40"><x-icon name="chevron" class="size-5 -rotate-90" /></span>
    @endif
</nav>
@endif

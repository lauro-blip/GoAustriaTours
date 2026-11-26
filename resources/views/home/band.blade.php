{{-- Home: four points band. Icons follow the position (1-4). --}}
@php
    $bandIco = [
        '<circle cx="24" cy="24" r="19"/><circle cx="24" cy="24" r="1.5"/><path d="M31 17l-4.5 9.5L17 31l4.5-9.5z"/><path d="M24 3v4M24 41v4M3 24h4M41 24h4"/>',
        '<path d="M8 42V15l16-8 16 8v27"/><path d="M4 42h40"/><path d="M19 42V31h10v11"/><path d="M15 21h4M29 21h4M15 26h4M29 26h4"/><path d="M24 2.5v4"/>',
        '<path d="M8 33a16 16 0 0 1 32 0z"/><path d="M5 38h38"/><path d="M24 13v4"/><path d="M20.5 12h7"/><path d="M13 33a11 11 0 0 1 6-9.5"/>',
        '<path d="M24 5v38"/><path d="M24 9h13l4 4-4 4H24z"/><path d="M24 21H11l-4 4 4 4h13z"/><path d="M24 33h11l3 3-3 3H24"/><path d="M18 43h12"/>',
    ];
@endphp
@if(! empty($d['items']))
<section class="band" aria-label="How we plan your Austria journey">
    <div class="wrap band-grid">
        @foreach($d['items'] as $it)
            <div class="bi rv"><span class="bic"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $bandIco[$loop->index % 4] !!}</svg></span>@if(filled($it['title'] ?? null))<h2{{ $ed('items.'.$loop->index.'.title') }}>{{ $tx($it['title']) }}</h2>@endif @if(filled($it['text'] ?? null))<p{{ $ed('items.'.$loop->index.'.text') }}>{{ $tx($it['text']) }}</p>@endif</div>
        @endforeach
    </div>
</section>
@endif

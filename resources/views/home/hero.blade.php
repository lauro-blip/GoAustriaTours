{{-- Home: hero (photos, headline, trip planner quiz, "Where to?" strip). Always first. --}}
@php
    $slides = collect([$d['slide1'] ?? null, $d['slide2'] ?? null, $d['slide3'] ?? null])->map(fn ($s) => \App\Support\Blocks::image($s))->filter()->values();
    $wayIco = [
        'self-drive' => '<path d="M5 17h14M6.5 17v2M17.5 17v2M4 13l1.6-4.6A2 2 0 0 1 7.5 7h9a2 2 0 0 1 1.9 1.4L20 13v4H4z"/><circle cx="7.5" cy="14" r="1"/><circle cx="16.5" cy="14" r="1"/>',
        'escorted' => '<circle cx="9" cy="7" r="3"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0M16 4v16M16 4l4.5 2.2L16 8.4"/>',
        'coach' => '<rect x="4" y="4" width="16" height="13" rx="2"/><path d="M4 11h16M8 17v2.5M16 17v2.5"/><circle cx="8" cy="14" r=".8"/><circle cx="16" cy="14" r=".8"/>',
    ];
    $hl = trim((string) ($d['h1_highlight'] ?? ''));
@endphp
<section class="hero">
    <div class="hero-bg" aria-hidden="true">
        @foreach($slides as $s)
            @if($loop->first)<img src="{{ $s['src'] }}" @if($s['srcset']) srcset="{{ $s['srcset'] }}" sizes="100vw" @endif alt="" fetchpriority="high">@else<img data-src="{{ $s['src'] }}" @if($s['srcset']) data-srcset="{{ $s['srcset'] }}" sizes="100vw" @endif alt="">@endif
        @endforeach
    </div>
    <div class="wrap hero-in">
        <div class="hero-copy">
            @if($tourTypes->isNotEmpty())
                <nav class="hero-ways" aria-label="{{ $activeTour ? $activeTour->name.' tours of Austria' : 'Ways to travel' }}">
                    @foreach($tourTypes->take(3) as $t)
                        <a href="{{ route('tours.show', $t) }}" @class(['on' => $activeTour?->is($t)]) @if($activeTour?->is($t)) aria-current="true" @endif>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $wayIco[$t->slug] ?? $wayIco['coach'] !!}</svg>
                            <span>{{ $t->name }}<i> tours</i></span>
                        </a>
                    @endforeach
                </nav>
            @endif
            <h1>{{ $tx($d['h1_before'] ?? '') }} @if($hl !== '')<span class="hl">{{ $tx($hl) }}<svg viewBox="0 0 300 24" preserveAspectRatio="none" aria-hidden="true"><path d="M4 15c48-7 108-10 168-9 44 .7 84 3.4 124 7.6"/><path d="M30 19.5c56-4.6 118-6 176-4.4 26 .7 50 2 72 3.6"/></svg></span>@endif {{ $tx($d['h1_after'] ?? '') }} @if(filled($d['h1_italic'] ?? null))<em{{ $ed('h1_italic') }}>{{ $tx($d['h1_italic']) }}</em>@endif</h1>
            <p class="sub">@if($activeTour && filled($activeTour->tagline)){{ $activeTour->tagline }}@else{{ $tx($d['sub'] ?? '') }}@endif</p>
        </div>
        <div class="hero-quiz" id="hero-quiz">
            @if(filled($d['quiz_tag'] ?? null))<span class="qtag"{{ $ed('quiz_tag') }}>{{ $tx($d['quiz_tag']) }}</span>@endif
            @if(filled($d['quiz_title'] ?? null))<p class="qtitle"{{ $ed('quiz_title') }}>{{ $tx($d['quiz_title']) }}</p>@endif
            @include('pages.partials.study-quiz')
        </div>
    </div>
    @if(filled($d['strip_label'] ?? null))
    <div class="strip" aria-label="{{ $d['strip_label'] }}">
        <b>{{ $tx($d['strip_label']) }}</b>
        <div class="mq"><div class="mq-track">
            @foreach([1, 2] as $copy)
                <div class="mq-set" @if($copy === 2) aria-hidden="true" @endif>@foreach($navPlaces as $list)@foreach($list as $pl)<a href="{{ route('packages.index', ['q' => $pl[1]]) }}" @if($copy === 2) tabindex="-1" @endif>{{ $pl[1] }}</a>@endforeach @endforeach</div>
            @endforeach
        </div></div>
    </div>
    @endif
</section>

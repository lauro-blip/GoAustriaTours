{{-- /travel-styles: a mosaic of the travel styles (config goaustria.travel_styles), then one compact chapter per style with the places,
     ways to travel and journeys that really match it (App\Support\TravelStyles). Styles: "Travel Styles page" at the end of inner.css (+ dst-* chapters). --}}
@php
    $words = [1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight'];
    $n = $styles->count();
    $bTitle = 'Travel Styles';
    $bEyebrow = 'Travel the way you love';
    $bLead = 'Palaces and concert halls, Alpine roads, lakeside villages or the Danube vineyards. Start with what you love and we shape the route around it.';
    $bImage = $w('xmas-vienna', '-1280');
    $bCrumbs = [['Travel Styles', null]];
    $bActions = view('pages.partials.cta-actions', ['caInterest' => 'Travel styles', 'caLabel' => 'Plan my Austria trip', 'site' => $site])->render();
    $lockup = function (string $name) {
        $parts = explode(' & ', $name, 2);
        return count($parts) === 2 ? e($parts[0]).' &amp; <em>'.e($parts[1]).'</em>' : e($name);
    };
    $plural = fn (int $c, string $one) => $c.' '.($c === 1 ? $one : $one.'s');
@endphp
@extends('layouts.site', [
    'metaTitle' => 'Austria travel styles: '.$styles->pluck('name')->take(4)->implode(', ').' | '.($site['brand_name'] ?? $brand),
    'metaDescription' => 'Find the Austria trip that suits you: '.strtolower($styles->pluck('name')->implode(', ')).'. See the journeys and places for each travel style.',
    'canonical' => route('styles.index'),
    'ctaTitle' => 'Love more than one style?',
    'ctaText' => 'Tell us what you enjoy and a travel specialist will shape one route around all of it.',
    'ctaInterest' => 'Travel styles',
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Travel Styles', route('styles.index')]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

{{-- ============ FIND YOUR STYLE (mosaic) ============ --}}
<section class="sec sty-find">
    <div class="wrap">
        <div class="vac-head">
            <p class="pk-k">Find your style</p>
            <h2 class="h">{{ $words[$n] ?? $n }} ways <em>to love Austria</em></h2>
            <p class="vac-lede">Each style shows the journeys that include it. Choose one to see where it takes you.</p>
        </div>
        <div class="sty-mosaic">
            @foreach($styles as $s)
                @php $pc = $s->packages->count(); @endphp
                <a class="sty-card" href="#{{ $s->slug }}">
                    <img src="{{ $s->img['small'] }}" alt="" loading="lazy">
                    <span class="sty-no" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <span class="sty-body">
                        @if($s->icon)<span class="sty-ic"><x-icon :name="$s->icon" :stroke="1" /></span>@endif
                        <b>{{ $s->name }}</b>
                        <em>{{ $s->text }}</em>
                        <u>@if($pc){{ $plural($pc, 'journey') }}@else Planned on request @endif <i aria-hidden="true">&darr;</i></u>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ ONE CHAPTER PER STYLE ============ --}}
@foreach($styles as $s)
@php $pc = $s->packages->count(); @endphp
<section @class(['sec', 'dst-ch', 'sty-ch', 'tint' => $loop->odd]) id="{{ $s->slug }}" aria-labelledby="sty-{{ $s->slug }}">
    <div class="wrap">
        <div @class(['dst-top', 'sty-top', 'flip' => $loop->even])>
            <figure class="dst-ph">
                <img src="{{ $s->img['src'] }}" @if($s->img['srcset']) srcset="{{ $s->img['srcset'] }}" sizes="(min-width: 1000px) 45vw, 100vw" @endif alt="{{ $s->name }} in Austria" loading="lazy">
                <span class="dst-no" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
            </figure>
            <div class="dst-copy">
                <p class="pk-k">Travel style {{ sprintf('%02d', $loop->iteration) }}</p>
                <h2 class="h" id="sty-{{ $s->slug }}">{!! $lockup($s->name) !!}</h2>
                <p class="dst-lede">{{ $s->text }}</p>
                @if($s->places->isNotEmpty())
                    <p class="sty-k">Where you will find it</p>
                    <ul class="sty-chips">@foreach($s->places->take(6) as $pl)<li><a href="{{ $pl['searchUrl'] }}">{{ $pl['name'] }}</a></li>@endforeach</ul>
                @endif
                @if($s->ways->isNotEmpty())
                    <p class="sty-k">Travel it by</p>
                    <p class="sty-ways">{{ $s->ways->implode(' · ') }}</p>
                @endif
                @if($pc)
                    <ul class="dst-jr sty-jr">
                        @foreach($s->packages->take(3) as $p)
                            @php $c = \App\Support\Places::card($p); @endphp
                            <li><a href="{{ route('packages.show', $p) }}">
                                <span class="dst-jr-t">{{ $p->title }}</span>
                                <span class="dst-jr-m">@if($p->nights){{ $p->nights + 1 }} days · {{ $p->nights }} nights @endif @if($p->tourType)<i>{{ $p->tourType->name }}</i>@endif</span>
                                <span class="dst-jr-p">@if($c->price)From <b>{{ $c->price }}</b>@else Price on request @endif</span>
                            </a></li>
                        @endforeach
                    </ul>
                @endif
                <div class="dst-act">
                    @if($pc)<a class="btn" href="{{ route('packages.index', ['style' => $s->slug]) }}">View {{ $plural($pc, 'journey') }} <i aria-hidden="true">&rarr;</i></a>@endif
                    <a @class(['link' => $pc, 'btn' => ! $pc]) href="{{ route('contact') }}" data-quote data-interest="{{ $s->name }}">Plan a {{ $s->name }} trip</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endforeach

@include('pages.partials.cta-strip', ['csTitle' => 'Not sure which style fits?', 'csText' => 'Tell us your dates and what you enjoy most. A travel specialist will suggest a route and send a free quote.', 'csInterest' => 'Travel styles', 'csButton' => 'Help me choose'])
@endsection

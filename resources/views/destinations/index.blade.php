{{-- /destinations: Austria at a glance (inline SVG map, no external map links), then one chapter per region with its verified places and journeys. Styles: "Destinations page" at the end of inner.css. --}}
@php
    $w = fn (string $name, string $size = '') => asset('images/winter/'.$name.$size.'.webp');
    $allPlaces = $regions->flatMap->places;
    $names = $regions->map(fn ($r) => $r->d->name)->all();
    $bTitle = 'Destinations';
    $bEyebrow = 'Where to go in Austria';
    $bLead = 'Imperial cities, lakeside villages and Alpine valleys. See where our journeys go, then let a travel specialist shape the route around you.';
    $bImage = $w('hallstatt', '-1280');
    $bCrumbs = [['Destinations', null]];
    $bActions = view('pages.partials.cta-actions', ['caInterest' => 'Austria destinations', 'caLabel' => 'Plan my Austria trip', 'site' => $site])->render();
    // "Vienna & Salzburg" -> "Vienna &" + italic "Salzburg" (the brand's serif lockup); single names stay as they are.
    $lockup = function (string $name) {
        $parts = explode(' & ', $name, 2);
        return count($parts) === 2 ? e($parts[0]).' &amp; <em>'.e($parts[1]).'</em>' : e($name);
    };
    $plural = fn (int $n, string $one) => $n.' '.($n === 1 ? $one : $one.'s');
    $pin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>';
@endphp
@extends('layouts.site', [
    'metaTitle' => 'Austria destinations: '.implode(', ', $names).' | '.($site['brand_name'] ?? $brand),
    'metaDescription' => 'Where to go in Austria: '.implode(' and ', $names).'. The places our journeys visit, with self drive, escorted and coach tours to each region.',
    'canonical' => route('destinations.index'),
    'ctaTitle' => 'Ready to explore Austria?',
    'ctaInterest' => 'Austria destinations',
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Destinations', route('destinations.index')]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

{{-- ============ AT A GLANCE ============ --}}
<section class="sec dst-glance">
    <div class="wrap hlx plx">
        <div class="hlx-intro">
            <p class="pk-k">Austria at a glance</p>
            <h2 class="hlx-h">{{ $plural($regions->count(), 'region') }}, <em>{{ $plural($allPlaces->count(), 'place') }} to discover</em></h2>
            <p class="hlx-lede">Every point on the map is a place one of our journeys really visits. Choose a region to see its places and journeys.</p>
            @if($allPlaces->isNotEmpty())
            <figure class="plx-map">
                <svg viewBox="{{ \App\Support\AustriaMap::viewBox() }}" role="img" aria-label="Map of Austria with the places our journeys visit: {{ $allPlaces->pluck('name')->implode(', ') }}">
                    <path class="plx-land" d="{{ \App\Support\AustriaMap::outline() }}"/>
                    @foreach($regions as $r)
                        @foreach($r->places as $pl)
                            <g class="plx-pt r{{ $loop->parent->iteration }}" data-plx="{{ $r->d->slug }}" transform="translate({{ $pl['xy'][0] }} {{ $pl['xy'][1] }})"><circle r="12"/><text y="4.4">{{ $pl['n'] }}</text></g>
                        @endforeach
                    @endforeach
                </svg>
                <figcaption>Stylised map. Positions are approximate.</figcaption>
            </figure>
            @endif
        </div>
        <ol class="hlx-list plx-list dst-regions">
            @foreach($regions as $r)
                @php $pc = $r->packages->count(); @endphp
                <li data-plx="{{ $r->d->slug }}">
                    <span class="hlx-n" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <div class="hlx-b">
                        <p class="hlx-meta">{{ $plural($pc, 'journey') }}<span>{{ $plural($r->places->count(), 'place') }}</span></p>
                        <h5>{{ $r->d->name }}</h5>
                        @if(filled($r->d->summary) && ! str_contains($r->d->summary, '[CONFIRM'))<p class="hlx-fact">{{ $r->d->summary }}</p>@endif
                        @if($r->places->isNotEmpty())<p class="dst-names">{{ $r->places->map(fn ($pl) => $pl['n'].' '.$pl['name'])->implode(' · ') }}</p>@endif
                        <a class="plx-more" href="#{{ $r->d->slug }}">Explore the region <i aria-hidden="true">&darr;</i></a>
                    </div>
                    <img class="hlx-th" src="{{ $w($r->image, '-768') }}" alt="{{ $r->d->name }}" loading="lazy">
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============ ONE CHAPTER PER REGION ============ --}}
@foreach($regions as $r)
@php $pc = $r->packages->count(); $flip = $loop->even; @endphp
<section @class(['sec', 'dst-ch', 'tint' => $loop->odd]) id="{{ $r->d->slug }}" aria-labelledby="dst-{{ $r->d->slug }}">
    <div class="wrap">
        <div @class(['dst-top', 'flip' => $flip])>
            <figure class="dst-ph">
                <img src="{{ $w($r->image, '-1280') }}" srcset="{{ $w($r->image, '-768') }} 768w, {{ $w($r->image, '-1280') }} 1280w" sizes="(min-width: 1000px) 50vw, 100vw" alt="{{ $r->d->name }}, Austria" loading="lazy">
                <span class="dst-no" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
            </figure>
            <div class="dst-copy">
                <p class="pk-k">Region {{ sprintf('%02d', $loop->iteration) }}</p>
                <h2 class="h" id="dst-{{ $r->d->slug }}">{!! $lockup($r->d->name) !!}</h2>
                @if(filled($r->d->summary) && ! str_contains($r->d->summary, '[CONFIRM'))<p class="dst-lede">{{ $r->d->summary }}</p>@endif
                <dl class="dst-facts">
                    <div><dt>Journeys</dt><dd>{{ $pc }}</dd></div>
                    <div><dt>Places</dt><dd>{{ $r->places->count() }}</dd></div>
                    @php $nights = $r->packages->pluck('nights')->filter(); @endphp
                    @if($nights->isNotEmpty())<div><dt>Nights</dt><dd>{{ $nights->min() === $nights->max() ? $nights->min() : $nights->min().'–'.$nights->max() }}</dd></div>@endif
                </dl>
                <div class="dst-act">
                    @if($pc)<a class="btn" href="{{ route('packages.index', ['destination' => $r->d->slug]) }}">View {{ $plural($pc, 'journey') }} <i aria-hidden="true">&rarr;</i></a>@endif
                    <a @class(['link' => $pc, 'btn' => ! $pc]) href="{{ route('contact') }}" data-quote data-interest="{{ $r->d->name }}">Plan a trip here</a>
                </div>
            </div>
        </div>

        @if($r->places->isNotEmpty() || $pc)
        <div class="dst-cols">
            @if($r->places->isNotEmpty())
            <div class="dst-pl">
                <h3 class="dst-sub">Places in {{ $r->d->name }}</h3>
                <ol class="hlx-list dst-places">
                    @foreach($r->places as $pl)
                        <li>
                            <span class="hlx-n" aria-hidden="true">{{ $pl['n'] }}</span>
                            <div class="hlx-b">
                                @if($pl['region'] !== $pl['name'])<p class="hlx-meta">{{ $pl['region'] }}</p>@endif
                                <h5>{{ $pl['name'] }}</h5>
                                <p class="hlx-fact">{{ $pl['fact'] }}</p>
                                <a class="plx-more" href="{{ $pl['searchUrl'] }}">Journeys to {{ $pl['name'] }} <i aria-hidden="true">&rarr;</i></a>
                            </div>
                            @if(! empty($pl['imageUrl']))<img class="hlx-th" src="{{ $pl['imageUrl'] }}" alt="{{ $pl['name'] }}" loading="lazy">@else<span class="hlx-ic">{!! $pin !!}</span>@endif
                        </li>
                    @endforeach
                </ol>
            </div>
            @endif
            @if($pc)
            <div class="dst-js">
                <h3 class="dst-sub">Journeys in {{ $r->d->name }}</h3>
                <ul class="dst-jr">
                    @foreach($r->packages->take(6) as $p)
                        @php $c = \App\Support\Places::card($p); @endphp
                        <li>
                            <a href="{{ route('packages.show', $p) }}">
                                <span class="dst-jr-t">{{ $p->title }}</span>
                                <span class="dst-jr-m">@if($p->nights){{ $p->nights + 1 }} days · {{ $p->nights }} nights @endif @if($p->tourType)<i>{{ $p->tourType->name }}</i>@endif</span>
                                <span class="dst-jr-p">@if($c->price)From <b>{{ $c->price }}</b>@else Price on request @endif</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                @if($pc > 6)<a class="plx-more" href="{{ route('packages.index', ['destination' => $r->d->slug]) }}">All {{ $pc }} journeys in {{ $r->d->name }} <i aria-hidden="true">&rarr;</i></a>@endif
            </div>
            @endif
        </div>
        @endif
    </div>
</section>
@endforeach

@include('pages.partials.cta-strip', ['csTitle' => 'Not sure which region suits you?', 'csText' => 'Tell us your dates and what you love. A travel specialist will suggest the right mix of cities, lakes and mountains and send a free quote.', 'csInterest' => 'Austria destinations', 'csButton' => 'Help me choose'])
@endsection

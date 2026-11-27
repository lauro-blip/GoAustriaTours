{{-- /destinations: Austria at a glance (inline SVG map, no external map links) beside a numbered list of every place our journeys visit,
     each with its journey count and a link to those journeys. Destinations are places, no regions. Styles: "Destinations page" in inner.css. --}}
@php
    $w = fn (string $name, string $size = '') => asset('images/winter/'.$name.$size.'.webp');
    $bTitle = 'Destinations';
    $bEyebrow = 'Where to go in Austria';
    $bLead = 'Imperial cities, lakeside villages and Alpine valleys. See where our journeys go, then let a travel specialist shape the route around you.';
    $bImage = $w('hallstatt', '-1280');
    $bCrumbs = [['Destinations', null]];
    $bActions = view('pages.partials.cta-actions', ['caInterest' => 'Austria destinations', 'caLabel' => 'Plan my Austria trip', 'site' => $site])->render();
    $plural = fn (int $n, string $one) => $n.' '.($n === 1 ? $one : $one.'s');
    $pin = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>';
    $names = $places->pluck('name');
@endphp
@extends('layouts.site', [
    'metaTitle' => 'Austria destinations: '.$names->take(5)->implode(', ').' | '.($site['brand_name'] ?? $brand),
    'metaDescription' => 'Where to go in Austria: '.$names->take(6)->implode(', ').' and more. The places our self drive, escorted and coach journeys visit.',
    'canonical' => route('destinations.index'),
    'ctaTitle' => 'Ready to explore Austria?',
    'ctaInterest' => 'Austria destinations',
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Destinations', route('destinations.index')]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

<section class="sec dst-glance">
    <div class="wrap hlx plx">
        <div class="hlx-intro">
            <p class="pk-k">Austria at a glance</p>
            <h2 class="hlx-h">{{ $plural($places->count(), 'place') }} <em>to discover</em></h2>
            <p class="hlx-lede">Every point on the map is a place one of our journeys really visits. Pick a place to see the journeys that go there.</p>
            @if($places->isNotEmpty())
            <figure class="plx-map">
                <svg viewBox="{{ \App\Support\AustriaMap::viewBox() }}" role="img" aria-label="Map of Austria with the places our journeys visit: {{ $names->implode(', ') }}">
                    <path class="plx-land" d="{{ \App\Support\AustriaMap::outline() }}"/>
                    @foreach($places as $key => $pl)
                        <g class="plx-pt r1" data-plx="{{ $key }}" transform="translate({{ $pl['xy'][0] }} {{ $pl['xy'][1] }})"><circle r="12"/><text y="4.4">{{ $pl['n'] }}</text></g>
                    @endforeach
                </svg>
                <figcaption>Stylised map. Positions are approximate.</figcaption>
            </figure>
            @endif
        </div>
        <ol class="hlx-list plx-list dst-all">
            @foreach($places as $key => $pl)
                <li data-plx="{{ $key }}" id="{{ $key }}">
                    <span class="hlx-n" aria-hidden="true">{{ $pl['n'] }}</span>
                    <div class="hlx-b">
                        <p class="hlx-meta">{{ $plural($pl['count'], 'journey') }}@if($pl['region'] !== $pl['name'])<span>{{ $pl['region'] }}</span>@endif</p>
                        <h5>{{ $pl['name'] }}</h5>
                        <p class="hlx-fact">{{ $pl['fact'] }}</p>
                        <a class="plx-more" href="{{ route('packages.index', ['place' => $key]) }}">View {{ $plural($pl['count'], 'journey') }} <i aria-hidden="true">&rarr;</i></a>
                    </div>
                    @if(! empty($pl['imageUrl']))<img class="hlx-th" src="{{ $pl['imageUrl'] }}" alt="{{ $pl['name'] }}" loading="lazy">@else<span class="hlx-ic">{!! $pin !!}</span>@endif
                </li>
            @endforeach
        </ol>
    </div>
</section>

@include('pages.partials.cta-strip', ['csTitle' => 'Not sure where to go?', 'csText' => 'Tell us your dates and what you love. A travel specialist will suggest the right mix of cities, lakes and mountains and send a free quote.', 'csInterest' => 'Austria destinations', 'csButton' => 'Help me choose'])
@endsection

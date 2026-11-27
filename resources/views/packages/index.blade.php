@php
    $brandName = $site['brand_name'] ?? 'GoAustriaTours';
    $filtered = $activePlace || $activeTourType || $activeStyle || $activeCollection || $searchTerm !== '';
    $bTitle = $activePlace ? 'Journeys to '.$activePlace['name'] : ($activeTourType ? $activeTourType->name.' tours' : ($activeStyle['name'] ?? ($activeCollection['name'] ?? 'Austria Tour Packages')));
    $bLead = $activePlace['fact'] ?? ($activeTourType?->tagline ?: ($activeStyle['text'] ?? ($activeCollection['text'] ?? 'Self drive, escorted and coach journeys through Austria, each one tailored to you.')));
    $bEyebrow = $filtered ? 'Journeys' : 'Our journeys';
    $bImage = ($activePlace ? \App\Support\Places::photo($activePlace['key'], 'lg') : null) ?? asset('images/winter/hero-wagrain-1280.webp');
    $bCrumbs = $activePlace ? [['Destinations', route('destinations.index')], [$activePlace['name'], null]] : [['Journeys', null]];
    $bActions = view('pages.partials.cta-actions', ['caLabel' => 'Help me choose', 'caInterest' => $activePlace['name'] ?? null, 'site' => $site])->render();
    $shown = $activePlace ? [$activePlace] : \App\Support\Places::inText($searchTerm);
@endphp
@extends('layouts.site', [
    'metaTitle' => 'Austria Tour Packages | '.($site['brand_name'] ?? ''),
    'metaDescription' => 'Browse self drive, escorted and coach tour packages in Austria.',
    'canonical' => route('packages.index'),
    'ctaTitle' => 'Not sure which journey is right for you?',
    'ctaText' => 'Tell us your dates and how you like to travel. A specialist will suggest the best fit and send a free quote.',
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Packages', route('packages.index')]]), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

<section class="sec">
    <div class="wrap">
        <form class="filters" method="GET" action="{{ route('packages.index') }}">
            <label>Tour type<select name="tour_type"><option value="">All tour types</option>@foreach($tourTypes as $t)<option value="{{ $t->slug }}" @selected($activeTourType?->id === $t->id)>{{ $t->name }}</option>@endforeach</select></label>
            <label>Destination<select name="place"><option value="">All destinations</option>@foreach($places as $key => $pl)<option value="{{ $key }}" @selected(($activePlace['key'] ?? null) === $key)>{{ $pl['name'] }}</option>@endforeach</select></label>
            <label>Travel style<select name="style"><option value="">All travel styles</option>@foreach(config('goaustria.travel_styles') as $slug => $st)<option value="{{ $slug }}" @selected(($activeStyle['slug'] ?? null) === $slug)>{{ $st['name'] }}</option>@endforeach</select></label>
            <button type="submit" class="btn">Filter <i aria-hidden="true">&rarr;</i></button>
        </form>

        <p class="rescount">
            {{ $packages->count() }} {{ $packages->count() === 1 ? 'journey' : 'journeys' }}
            @if($activeCollection) in <strong>{{ $activeCollection['name'] }}</strong>@endif
            @if($activeStyle) matching <strong>{{ $activeStyle['name'] }}</strong>@endif
            @if($searchTerm !== '') that include <strong>{{ $searchTerm }}</strong>@endif
            @if($activePlace) to <strong>{{ $activePlace['name'] }}</strong>@endif
            @if($filtered) &middot; <a href="{{ route('packages.index') }}">Show all journeys</a>@endif
        </p>

        @forelse($packages as $p)
            @if($loop->first)<div class="grid">@endif
            <div class="cell">@include('pages.partials.package-card', ['f' => \App\Support\Places::card($p)])</div>
            @if($loop->last)</div>@endif
        @empty
            <p class="empty">No journeys match those filters. <a href="{{ route('packages.index') }}">Clear the filters</a> or <a href="{{ route('contact') }}" data-quote>tell us what you have in mind</a>.</p>
        @endforelse
    </div>
</section>

@include('pages.partials.cta-strip', ['csTitle' => 'Can’t see the right journey?', 'csText' => 'Every itinerary can be changed, and we can build one from scratch. Tell us what you have in mind.', 'csButton' => 'Plan my own journey'])

@if(count($shown))
<section class="sec">{{-- cream: the cta-strip above is navy, the footer CTA below is tint --}}
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Places to know</p><h2 class="h">About {{ $activePlace ? $activePlace['name'] : 'these places' }}</h2></div>
        @include('pages.partials.place-cards', ['places' => $shown])
    </div>
</section>
@endif
@endsection

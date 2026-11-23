@php
    $brandName = $site['brand_name'] ?? 'GoAustriaTours';
    $bTitle = $activeDestination?->name ?? ($activeTourType ? $activeTourType->name.' tours' : ($activeStyle['name'] ?? ($activeCollection['name'] ?? 'Austria Tour Packages')));
    $bLead = $activeDestination?->summary ?: ($activeTourType?->tagline ?: ($activeStyle['text'] ?? ($activeCollection['text'] ?? 'Self drive, escorted and coach journeys through Austria, each one tailored to you.')));
    $bEyebrow = ($activeDestination || $activeTourType || $activeStyle || $activeCollection || $searchTerm !== '') ? 'Journeys' : 'Our journeys';
    $bImage = asset('images/winter/'.($activeDestination ? ($activeDestination->slug === 'tyrol' ? 'hero-montafon' : 'hero-salzburg') : 'hero-wagrain').'-1280.webp');
    $bCrumbs = [['Journeys', null]];
    $shown = \App\Support\Places::inText(($activeDestination?->summary ?? '').' '.$searchTerm);
    if ($activeDestination) {
        $shown = collect(config('goaustria.nav_places.'.$activeDestination->slug, []))->flatMap(fn ($pl) => \App\Support\Places::inText($pl[1]))->unique('key')->values()->all();
    }
@endphp
@extends('layouts.site', [
    'metaTitle' => 'Austria Tour Packages | '.($site['brand_name'] ?? ''),
    'metaDescription' => 'Browse self drive, escorted and coach tour packages in Austria.',
    'canonical' => route('packages.index'),
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
            <label>Destination<select name="destination"><option value="">All destinations</option>@foreach($destinations as $d)<option value="{{ $d->slug }}" @selected($activeDestination?->id === $d->id)>{{ $d->name }}</option>@endforeach</select></label>
            <label>Travel style<select name="style"><option value="">All travel styles</option>@foreach(config('goaustria.travel_styles') as $slug => $st)<option value="{{ $slug }}" @selected(($activeStyle['slug'] ?? null) === $slug)>{{ $st['name'] }}</option>@endforeach</select></label>
            <button type="submit" class="btn">Filter <i aria-hidden="true">&rarr;</i></button>
        </form>

        <p class="rescount">
            {{ $packages->count() }} {{ $packages->count() === 1 ? 'journey' : 'journeys' }}
            @if($activeCollection) in <strong>{{ $activeCollection['name'] }}</strong>@endif
            @if($activeStyle) matching <strong>{{ $activeStyle['name'] }}</strong>@endif
            @if($searchTerm !== '') that include <strong>{{ $searchTerm }}</strong>@endif
            @if($activeDestination || $activeTourType || $activeStyle || $activeCollection || $searchTerm !== '') &middot; <a href="{{ route('packages.index') }}">Show all journeys</a>@endif
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

@if(count($shown))
<section class="sec tint">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Places to know</p><h2 class="h">About {{ $activeDestination ? 'this region' : 'these places' }}</h2></div>
        @include('pages.partials.place-cards', ['places' => $shown])
    </div>
</section>
@endif
@endsection

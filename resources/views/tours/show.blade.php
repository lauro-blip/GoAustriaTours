@php
    $wayImg = ['self-drive' => 'way-self-drive', 'escorted' => 'way-escorted', 'coach' => 'way-coach'];
    $bTitle = $tourType->name.' tours of Austria';
    $bEyebrow = $tourType->tagline;
    $bLead = $tourType->summary;
    $bImage = asset('images/winter/'.($wayImg[$tourType->slug] ?? 'hero-wagrain').'-1280.webp');
    $bCrumbs = [['Tours', route('packages.index')], [$tourType->name, null]];
    $bActions = view('pages.partials.cta-actions', ['caInterest' => $tourType->name.' tour', 'caLabel' => 'Get a free '.$tourType->name.' quote', 'site' => $site])->render();
    $visited = collect($packages)->flatMap(fn ($p) => \App\Support\Places::forPackage($p))->unique('key')->values()->all();
    if (! count($visited)) { $visited = \App\Support\Places::inText(strip_tags((string) $tourType->body).' '.$tourType->summary); }
    $hasBody = trim(strip_tags((string) $tourType->body)) !== '' && ! str_contains((string) $tourType->body, '[CONFIRM');
@endphp
@extends('layouts.site', [
    'metaTitle' => $tourType->meta_title ?: $tourType->name.' tours of Austria | '.($site['brand_name'] ?? ''),
    'metaDescription' => $tourType->meta_description,
    'canonical' => route('tours.show', $tourType),
    'ctaTitle' => 'Ready to plan your '.$tourType->name.' tour?',
    'ctaInterest' => $tourType->name.' tour',
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], [$tourType->name.' Tours', route('tours.show', $tourType)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

@if($hasBody)
<section class="sec">
    <div class="wrap narrow"><div class="prose">{!! $tourType->body !!}</div></div>
</section>
@endif

@if($packages->isNotEmpty())
<section class="sec @if($hasBody) tint @endif">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Choose a journey</p><h2 class="h">{{ $tourType->name }} journeys</h2></div>
        <div class="grid">@foreach($packages as $p)<div class="cell">@include('pages.partials.package-card', ['f' => \App\Support\Places::card($p)])</div>@endforeach</div>
    </div>
</section>
@endif

@include('pages.partials.cta-strip', ['csTitle' => 'Not sure which '.$tourType->name.' journey is right?', 'csText' => 'Tell us your dates and how you like to travel. We will suggest the best fit and send a free quote.', 'csInterest' => $tourType->name.' tour'])

<section class="sec">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Simple and personal</p><h2 class="h">How it works</h2></div>
        <div class="steps">
            <div><b>1</b><h3>Tell us your plans</h3><p>Share your dates and how you like to travel. It takes about a minute.</p></div>
            <div><b>2</b><h3>We shape your route</h3><p>A travel specialist plans a {{ strtolower($tourType->name) }} journey around you and sends a free quote.</p></div>
            <div><b>3</b><h3>You enjoy Austria</h3><p>Hotels, routes and details are arranged, with one personal contact throughout.</p></div>
        </div>
    </div>
</section>

@if(count($visited))
<section class="sec tint">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Where {{ $tourType->name }} tours go</p><h2 class="h">Places on these journeys</h2></div>
        @include('pages.partials.place-cards', ['places' => $visited])
    </div>
</section>
@endif
@endsection

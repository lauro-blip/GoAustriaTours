@php
    $wayImg = ['self-drive' => 'way-self-drive', 'escorted' => 'way-escorted', 'coach' => 'way-coach'];
    $bTitle = $tourType->name.' tours of Austria';
    $bEyebrow = $tourType->tagline;
    $bLead = $tourType->summary;
    $bImage = asset('images/winter/'.($wayImg[$tourType->slug] ?? 'hero-wagrain').'-1280.webp');
    $bCrumbs = [['Tours', route('packages.index')], [$tourType->name, null]];
    $visited = collect($packages)->flatMap(fn ($p) => \App\Support\Places::forPackage($p))->unique('key')->values()->all();
    if (! count($visited)) { $visited = \App\Support\Places::inText(strip_tags((string) $tourType->body).' '.$tourType->summary); }
@endphp
@extends('layouts.site', [
    'metaTitle' => $tourType->meta_title ?: $tourType->name.' tours of Austria | '.($site['brand_name'] ?? ''),
    'metaDescription' => $tourType->meta_description,
    'canonical' => route('tours.show', $tourType),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], [$tourType->name.' Tours', route('tours.show', $tourType)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

<section class="sec">
    <div class="wrap pk">
        <div class="pk-main">
            @if(trim(strip_tags((string) $tourType->body)) !== '' && ! str_contains((string) $tourType->body, '[CONFIRM'))<div class="prose">{!! $tourType->body !!}</div>@endif
            @if($packages->isNotEmpty())
                <h2 class="pk-h">{{ $tourType->name }} journeys</h2>
                <div class="grid g2">@foreach($packages as $p)<div class="cell">@include('pages.partials.package-card', ['f' => \App\Support\Places::card($p)])</div>@endforeach</div>
            @endif
        </div>
        <aside class="pk-side" aria-label="Get a free quote">
            <div class="pk-card">
                <h2>Get a free {{ $tourType->name }} quote</h2>
                @include('pages.partials.quote-form', ['formName' => 'package-quote', 'uid' => 'tour', 'button' => 'Request my quote'])
            </div>
        </aside>
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

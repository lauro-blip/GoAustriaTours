{{-- /austria-vacations: the ways to travel compared side by side (real package data only), then one chapter per tour type with its journeys.
     Reuses the Destinations page styles (dst-*) + "Austria Vacations page" (vac-*) at the end of inner.css. --}}
@php
    $words = [1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five'];
    $n = $types->count();
    $bTitle = 'Austria Vacations';
    $bEyebrow = ($words[$n] ?? $n).' ways to travel';
    $bLead = 'Drive yourself, travel with a guide or tour by coach. Choose how you want to see Austria and a travel specialist plans the rest around your dates.';
    $bImage = $w('hero-montafon', '-1280');
    $bCrumbs = [['Austria Vacations', null]];
    $bActions = view('pages.partials.cta-actions', ['caInterest' => 'Austria vacation', 'caLabel' => 'Plan my Austria vacation', 'site' => $site])->render();
    $plural = fn (int $c, string $one) => $c.' '.($c === 1 ? $one : $one.'s');
    $clean = fn (?string $v) => filled($v) && ! str_contains($v, '[CONFIRM') ? $v : null;
@endphp
@extends('layouts.site', [
    'metaTitle' => 'Austria vacations: '.$types->map(fn ($x) => $x->t->name)->implode(', ').' tours | '.($site['brand_name'] ?? $brand),
    'metaDescription' => 'Compare the ways to travel Austria: '.$types->map(fn ($x) => strtolower($x->t->name))->implode(', ').' tours. Journeys, trip lengths and prices side by side, planned around your dates.',
    'canonical' => route('tours.index'),
    'ctaTitle' => 'Ready to plan your Austria vacation?',
    'ctaInterest' => 'Austria vacation',
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Austria Vacations', route('tours.index')]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

{{-- ============ COMPARE ============ --}}
<section class="sec vac-glance">
    <div class="wrap">
        <div class="vac-head">
            <p class="pk-k">Compare at a glance</p>
            <h2 class="h">{{ $words[$n] ?? $n }} ways <em>to see Austria</em></h2>
            <p class="vac-lede">Every option is a private quote planned around you. Here is how they compare, from the journeys we offer today.</p>
        </div>
        <div class="vac-cmp" style="--cols: {{ max(1, $n) }}">
            <div class="vac-labels" aria-hidden="true">
                <span></span><span>How it works</span><span>Journeys</span><span>Length</span><span>From</span><span>Regions</span><span></span>
            </div>
            @foreach($types as $x)
                @php $pc = $x->packages->count(); @endphp
                <article class="vac-col">
                    <a class="vac-top" href="{{ route('tours.show', $x->t) }}">
                        <img src="{{ $w($x->image, '-768') }}" alt="" loading="lazy">
                        <span class="vac-no" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                        <h3>{{ $x->t->name }}</h3>
                        @if($clean($x->t->tagline))<p class="vac-tag">{{ $x->t->tagline }}</p>@endif
                    </a>
                    <dl class="vac-rows">
                        <div><dt>How it works</dt><dd class="vac-sum">{{ $clean($x->t->summary) ?? '–' }}</dd></div>
                        <div><dt>Journeys</dt><dd class="vac-big">{{ $pc }}</dd></div>
                        <div><dt>Length</dt><dd>@if($x->nights)<b>{{ $x->nights }}</b> nights @else – @endif</dd></div>
                        <div><dt>From</dt><dd>@if($x->price)<b>{{ $x->price }}</b> <small>per person</small>@else Price on request @endif</dd></div>
                        <div><dt>Regions</dt><dd>{{ $x->regions->isNotEmpty() ? $x->regions->implode(' · ') : '–' }}</dd></div>
                    </dl>
                    <div class="vac-go">
                        <a class="btn" href="{{ route('tours.show', $x->t) }}">Explore {{ $x->t->name }} <i aria-hidden="true">&rarr;</i></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ ONE CHAPTER PER WAY TO TRAVEL ============ --}}
@foreach($types as $x)
@php $pc = $x->packages->count(); $t = $x->t; @endphp
<section @class(['sec', 'dst-ch', 'vac-ch', 'tint' => $loop->odd]) id="{{ $t->slug }}" aria-labelledby="vac-{{ $t->slug }}">
    <div class="wrap">
        <div @class(['dst-top', 'flip' => $loop->even])>
            <figure class="dst-ph">
                <img src="{{ $w($x->image, '-1280') }}" srcset="{{ $w($x->image, '-768') }} 768w, {{ $w($x->image, '-1280') }} 1280w" sizes="(min-width: 1000px) 50vw, 100vw" alt="{{ $t->name }} tour in Austria" loading="lazy">
                <span class="dst-no" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
            </figure>
            <div class="dst-copy">
                <p class="pk-k">Way to travel {{ sprintf('%02d', $loop->iteration) }}</p>
                <h2 class="h" id="vac-{{ $t->slug }}">{{ $t->name }}@if($clean($t->tagline))<em>{{ $t->tagline }}</em>@endif</h2>
                @if($clean($t->summary))<p class="dst-lede">{{ $t->summary }}</p>@endif
                <dl class="dst-facts">
                    <div><dt>Journeys</dt><dd>{{ $pc }}</dd></div>
                    @if($x->nights)<div><dt>Nights</dt><dd>{{ $x->nights }}</dd></div>@endif
                    @if($x->price)<div><dt>From</dt><dd>{{ $x->price }}</dd></div>@endif
                </dl>
                <div class="dst-act">
                    <a class="btn" href="{{ route('tours.show', $t) }}">Explore {{ $t->name }} tours <i aria-hidden="true">&rarr;</i></a>
                    <a class="link" href="{{ route('contact') }}" data-quote data-interest="{{ $t->name }} tour">Get a {{ $t->name }} quote</a>
                </div>
            </div>
        </div>

        @if($pc)
        <div class="vac-jr">
            <h3 class="dst-sub">{{ $t->name }} journeys</h3>
            <ul class="dst-jr">
                @foreach($x->packages->take(4) as $p)
                    @php $c = \App\Support\Places::card($p); @endphp
                    <li>
                        <a href="{{ route('packages.show', $p) }}">
                            <span class="dst-jr-t">{{ $p->title }}</span>
                            <span class="dst-jr-m">@if($p->nights){{ $p->nights + 1 }} days · {{ $p->nights }} nights @endif @if($p->destination)<i>{{ $p->destination->name }}</i>@endif</span>
                            <span class="dst-jr-p">@if($c->price)From <b>{{ $c->price }}</b>@else Price on request @endif</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            @if($pc > 4)<a class="plx-more" href="{{ route('tours.show', $t) }}">All {{ $pc }} {{ $t->name }} journeys <i aria-hidden="true">&rarr;</i></a>@endif
        </div>
        @endif
    </div>
</section>
@endforeach

{{-- ============ HOW IT WORKS ============ --}}
<section class="sec">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Simple and personal</p><h2 class="h">How it works</h2></div>
        <div class="steps">
            <div><b>1</b><h3>Tell us your plans</h3><p>Share your dates and how you like to travel. It takes about a minute.</p></div>
            <div><b>2</b><h3>We shape your route</h3><p>A travel specialist plans your journey around you and sends a free quote.</p></div>
            <div><b>3</b><h3>You enjoy Austria</h3><p>Hotels, routes and details are arranged, with one personal contact throughout.</p></div>
        </div>
    </div>
</section>

@include('pages.partials.cta-strip', ['csTitle' => 'Not sure which way to travel?', 'csText' => 'Tell us your dates, your group and the pace you enjoy. A travel specialist will suggest the best fit and send a free quote.', 'csInterest' => 'Austria vacation', 'csButton' => 'Help me choose'])
@endsection

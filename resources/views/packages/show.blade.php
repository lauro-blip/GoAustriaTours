@php
    $imgUrl = $package->hero_image ? asset('storage/'.$package->hero_image) : asset('images/packages/'.$package->slug.'.webp');
    $price = $package->price_from ? '$'.number_format($package->price_from) : null;
    $placesOn = \App\Support\Places::forPackage($package);
    $hasDesc = trim(strip_tags((string) $package->description)) !== '' && ! str_contains((string) $package->description, '[CONFIRM');
    $days = $package->nights + 1;

    // The places in the order the itinerary reaches them.
    $itText = mb_strtolower(collect($package->itinerary ?? [])->map(fn ($d) => ($d['title'] ?? '').' '.($d['body'] ?? ''))->implode(' '));
    $route = collect($placesOn)->sortBy(function ($pl) use ($itText) {
        $pos = PHP_INT_MAX;
        foreach ($pl['terms'] as $t) { $p = mb_stripos($itText, mb_strtolower($t)); if ($p !== false) { $pos = min($pos, $p); } }
        return $pos;
    })->values();

    $styleText = trim(strip_tags((string) $package->tourType?->summary));
    $styleText = str_contains($styleText, '[CONFIRM') ? '' : $styleText;

    $bTitle = $package->title;
    $bEyebrow = trim(($package->tourType?->name ?? '').' · '.$package->nights.' nights', ' ·');
    $bLead = $package->summary;
    $bImage = $imgUrl;
    $bCrumbs = [['Journeys', route('packages.index')], [$package->title, null]];
    $bActions = view('pages.partials.cta-actions', ['caPackage' => $package, 'site' => $site])->render();
    $bAside = view('pages.partials.package-hero-card', ['package' => $package, 'site' => $site])->render();
    $nav = array_values(array_filter([
        ['overview', 'Overview'],
        $package->itinerary ? ['itinerary', 'Itinerary'] : null,
        count($placesOn) ? ['places', 'Places'] : null,
        ($package->inclusions || $package->exclusions) ? ['included', 'What is included'] : null,
        $stories->isNotEmpty() ? ['reviews', 'Guest stories'] : null,
        $package->faqs ? ['faq', 'FAQ'] : null,
    ]));
@endphp
@extends('layouts.site', ['noStickyBar' => true,
    'metaTitle' => $package->seo_title ?: $package->title.' | '.($site['brand_name'] ?? ''),
    'metaDescription' => $package->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $package->summary), 155),
    'canonical' => route('packages.show', $package),
    'noindex' => $package->noindex,
    'ogImage' => $package->social_image ? asset('storage/'.$package->social_image) : $imgUrl,
    'ctaTitle' => 'Ready to plan '.$package->title.'?',
    'ctaText' => 'We tailor every journey to your dates and pace. Tell us what you have in mind and we will send a free quote.',
    'ctaPackage' => $package,
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::trip($package), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Packages', route('packages.index')], [$package->title, route('packages.show', $package)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@php $faqSchema = \App\Support\Schema::faq($package->faqs ?: []); @endphp
@if($faqSchema)
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
@endpush

@section('content')
@include('pages.partials.banner')

<section class="factsbar" aria-label="Tour at a glance">
    <div class="wrap"><dl>
        <div><dt>Duration</dt><dd>{{ $days }} days &middot; {{ $package->nights }} nights</dd></div>
        @if($package->tourType)<div><dt>Style</dt><dd>{{ $package->tourType->name }}</dd></div>@endif
        @if($package->destination)<div><dt>Region</dt><dd>{{ $package->destination->name }}</dd></div>@endif
        <div class="fprice"><dt>{{ $price ? 'From' : 'Price' }}</dt><dd>{{ $price ?: 'On request' }}@if($price)<small> per person</small>@endif</dd></div>
    </dl></div>
</section>

<nav class="pnav" aria-label="On this page" data-pnav>
    <div class="wrap pnav-in">
        <ul>@foreach($nav as [$nid, $nl])<li><a href="#{{ $nid }}">{{ $nl }}</a></li>@endforeach</ul>
        <div class="pnav-cta">
            <span>@if($price)<small>From</small> <b>{{ $price }}</b>@else<b>Price on request</b>@endif</span>
            <a class="btn sm" href="{{ route('contact') }}" data-quote data-package-id="{{ $package->id }}" data-package="{{ $package->title }}">Get a free quote</a>
        </div>
    </div>
</nav>

@if($route->count() >= 2)
<section class="routesec" aria-label="Your route">
    <div class="wrap">
        <p class="routesec-k">Your route</p>
        <ol class="route">@foreach($route->take(8) as $pl)<li><span class="rn">{{ $loop->iteration }}</span><a href="{{ $pl['mapUrl'] }}" target="_blank" rel="noopener"><b>{{ $pl['name'] }}</b><small>{{ $pl['region'] }}</small></a></li>@endforeach</ol>
    </div>
</section>
@endif

<section class="sec" id="overview">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">The journey</p><h2 class="h">About this journey</h2></div>
        @if($hasDesc)<div class="prose ov-prose">{!! $package->description !!}</div>
        @elseif($package->summary && ! $package->highlights)<div class="prose ov-prose"><p>{{ $package->summary }}</p></div>@endif
        @if($package->highlights)
            @if($hasDesc)<h3 class="hlc-h">Journey highlights</h3>@endif
            <ul class="hlc">@foreach($package->highlights as $h)<li><span class="hn" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span><p>{{ $h }}</p></li>@endforeach</ul>
        @endif
    </div>
</section>

@if($package->itinerary)
<section class="sec tint" id="itinerary">
    <div class="wrap narrow">
        <div class="head"><p class="eyebrow dark">Day by day</p><h2 class="h">Your itinerary</h2></div>
        <p class="tl-tools"><button type="button" class="tl-toggle" data-tl-toggle aria-pressed="false">Collapse all days</button></p>
        <ol class="tl" data-tl>
            @foreach($package->itinerary as $day)
                @php
                    $dt = (string) ($day['title'] ?? '');
                    $label = 'Day '.$loop->iteration;
                    $ranged = false;
                    if (preg_match('/^\s*(Days?\s*\d+(?:\s*[-–—]\s*\d+)?)\s*[:\-–—]\s*(.+)$/iu', $dt, $m)) { $label = trim($m[1]); $dt = $m[2]; $ranged = true; }
                    $dayPlaces = \App\Support\Places::inText($dt.' '.($day['body'] ?? ''));
                    $dayImg = collect($dayPlaces)->first(fn ($pl) => ! empty($pl['imageUrl']));
                @endphp
                <li>
                    <span class="tl-n" aria-hidden="true">@unless($ranged){{ $loop->iteration }}@endunless</span>
                    <details class="day" open>
                        <summary><span><small class="tl-d">{{ $label }}</small><h3>{{ $dt }}</h3></span><i class="chev" aria-hidden="true"></i></summary>
                        <div class="day-body @if($dayImg) has-img @endif">
                            @if($dayImg)<img class="day-img" src="{{ $dayImg['imageUrl'] }}" alt="{{ $dayImg['name'] }}" loading="lazy" width="320" height="240">@endif
                            <div>
                                <p>{{ $day['body'] ?? '' }}</p>
                                @if(count($dayPlaces))<p class="day-pl">@foreach($dayPlaces as $pl)<a href="{{ $pl['mapUrl'] }}" target="_blank" rel="noopener">{{ $pl['name'] }}<i aria-hidden="true">&nearr;</i></a>@endforeach</p>@endif
                            </div>
                        </div>
                    </details>
                </li>
            @endforeach
        </ol>
        <p class="tl-end"><a class="btn" href="{{ route('contact') }}" data-quote data-package-id="{{ $package->id }}" data-package="{{ $package->title }}">Request this itinerary <i aria-hidden="true">&rarr;</i></a><span>We can adjust any day to suit you.</span></p>
    </div>
</section>
@endif

@include('pages.partials.cta-strip', ['csTitle' => 'Like what you see?', 'csText' => 'A travel specialist will shape this journey around your dates, pace and interests, and send you a free quote.', 'csPackage' => $package])

@if(count($placesOn))
<section class="sec" id="places">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Where you will go</p><h2 class="h">Places on this journey</h2></div>
        @include('pages.partials.place-cards', ['places' => $route->all()])
    </div>
</section>
@endif

@if($package->inclusions || $package->exclusions)
<section class="sec tint" id="included">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">The details</p><h2 class="h">What is included</h2></div>
        <div class="incl">
            @if($package->inclusions)<div><h3>Included</h3><ul class="yes">@foreach($package->inclusions as $i)<li>{{ $i }}</li>@endforeach</ul></div>@endif
            @if($package->exclusions)<div><h3>Not included</h3><ul class="no">@foreach($package->exclusions as $i)<li>{{ $i }}</li>@endforeach</ul></div>@endif
        </div>
    </div>
</section>
@endif

@if($package->tourType && $styleText !== '')
<section class="sec">
    <div class="wrap narrow">
        <div class="stylebox">
            <p class="eyebrow dark">How {{ strtolower($package->tourType->name) }} travel works</p>
            <p>{{ $styleText }}</p>
            <a class="more-link" href="{{ route('tours.show', $package->tourType) }}">Explore all {{ $package->tourType->name }} tours <i aria-hidden="true">&rarr;</i></a>
        </div>
    </div>
</section>
@endif

<section class="sec tint">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Simple and personal</p><h2 class="h">How it works</h2></div>
        <div class="steps">
            <div><b>1</b><h3>Tell us your plans</h3><p>Share your dates and how you like to travel. It takes about a minute.</p></div>
            <div><b>2</b><h3>We shape your route</h3><p>A travel specialist adjusts this journey around you and sends a free quote.</p></div>
            <div><b>3</b><h3>You enjoy Austria</h3><p>Hotels, routes and details are arranged, with one personal contact throughout.</p></div>
        </div>
    </div>
</section>

@if($stories->isNotEmpty())
<section class="sec" id="reviews">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">In their own words</p><h2 class="h">Guest stories</h2></div>
        <div class="tgrid tcards">
            @foreach($stories as $t)
                @php $ini = collect(preg_split('/\s+/', trim($t->name)))->filter()->take(2)->map(fn ($x) => mb_strtoupper(mb_substr($x, 0, 1)))->implode(''); @endphp
                <figure class="rev">
                    <blockquote>{{ $t->quote }}</blockquote>
                    <figcaption><span class="av" aria-hidden="true">{{ $ini }}</span><span class="who"><b>{{ $t->name }}</b>@if($t->location)<small>{{ $t->location }}</small>@endif</span></figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($package->faqs)
<section class="sec tint" id="faq">
    <div class="wrap narrow">
        <div class="head"><p class="eyebrow dark">Good to know</p><h2 class="h">Questions about this tour</h2></div>
        <div class="faq">@foreach($package->faqs as $f)<details><summary>{{ $f['question'] }}</summary><p>{{ $f['answer'] }}</p></details>@endforeach</div>
        <p class="faq-ask">Still have a question? <a href="{{ route('contact') }}" data-quote data-package-id="{{ $package->id }}" data-package="{{ $package->title }}" data-interest="a question about {{ $package->title }}">Ask a specialist</a></p>
    </div>
</section>
@endif

@if($related->isNotEmpty())
<section class="sec">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Keep exploring</p><h2 class="h">You may also like</h2></div>
        <div class="grid">@foreach($related as $f)<div class="cell">@include('pages.partials.package-card', ['f' => $f])</div>@endforeach</div>
    </div>
</section>
@endif

<div class="pbar">
    <p><small>{{ $price ? 'From' : 'Price' }}</small><b>{{ $price ?: 'On request' }}</b></p>
    <a class="btn sm" href="{{ route('contact') }}" data-quote data-package-id="{{ $package->id }}" data-package="{{ $package->title }}">Get a free quote</a>
</div>
@endsection

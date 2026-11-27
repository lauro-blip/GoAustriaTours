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

    $bTitle = $package->title;
    $bEyebrow = trim(($package->tourType?->name ?? '').' · '.$package->nights.' nights', ' ·');
    $bLead = $package->summary;
    $bImage = $imgUrl;
    $bCrumbs = [['Journeys', route('packages.index')], [$package->title, null]];
    $bActions = view('pages.partials.cta-actions', ['caPackage' => $package, 'site' => $site])->render();
    $bAside = view('pages.partials.package-hero-card', ['package' => $package, 'site' => $site])->render();

    // Itinerary tabs: only the ones with content.
    $tabs = array_values(array_filter([
        $package->itinerary ? ['days', 'Itinerary details'] : null,
        $package->highlights ? ['highlights', 'Tour highlights'] : null,
        ($package->inclusions || $package->exclusions) ? ['included', 'Package inclusions'] : null,
        count($placesOn) ? ['places', 'Places & map'] : null,
    ]));
    $quote = fn (string $extra = '') => 'href="'.route('contact').'" data-quote data-package-id="'.$package->id.'" data-package="'.e($package->title).'"'.$extra;
    $ico = [
        'cal' => '<rect x="3" y="5" width="18" height="16" rx="1.5"/><path d="M3 10h18M8 3v4M16 3v4M7 13.5h2M11 13.5h2M15 13.5h2M7 17h2M11 17h2"/>',
        'bed' => '<path d="M3 18V7M3 14h18v4M21 14v-2.5a2.5 2.5 0 0 0-2.5-2.5H11v5"/><circle cx="7" cy="11" r="1.8"/>',
        'compass' => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/>',
        'pin' => '<path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'chat' => '<path d="M4 5h16v11H9l-5 4z"/><path d="M8 9.5h8M8 12.5h5"/>',
        'route' => '<circle cx="6" cy="19" r="2"/><circle cx="18" cy="5" r="2"/><path d="M8 19h7a3 3 0 0 0 0-6H9a3 3 0 0 1 0-6h7"/>',
        'sun' => '<path d="M3 20h18M7 20a5 5 0 0 1 10 0M12 4v3M4.9 8.9l2.1 2.1M19.1 8.9 17 11"/>',
    ];
    // Tour highlights, enriched with the verified place info we hold (photo, region, fact, map). Each place is used once;
    // highlights without a place (guide, car, small groups...) get a thin-line icon picked from their wording.
    $ico += [
        'car' => '<path d="M5 17h14M6.5 17v2M17.5 17v2M4 13l1.6-4.6A2 2 0 0 1 7.5 7h9a2 2 0 0 1 1.9 1.4L20 13v4H4z"/><circle cx="7.5" cy="14" r=".8"/><circle cx="16.5" cy="14" r=".8"/>',
        'guide' => '<circle cx="9" cy="7" r="3"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0M16 4v16M16 4l4.5 2.2L16 8.4"/>',
        'bus' => '<rect x="4" y="4" width="16" height="13" rx="2"/><path d="M4 11h16M8 17v2.5M16 17v2.5"/><circle cx="8" cy="14" r=".8"/><circle cx="16" cy="14" r=".8"/>',
        'wine' => '<path d="M8 3h8l-.5 5a3.5 3.5 0 0 1-7 0zM12 11.5V20M8.5 20h7M8.2 6h7.6"/>',
        'food' => '<path d="M7 3v8M5 3v4a2 2 0 0 0 4 0V3M7 11v10M16 3c-1.7 1.2-2.5 3.5-2.5 6.5H16V21"/>',
        'music' => '<path d="M9 18V5l11-2v13"/><circle cx="6.5" cy="18" r="2.5"/><circle cx="17.5" cy="16" r="2.5"/>',
        'users' => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20a6 6 0 0 1 12 0M16 4.8a3.2 3.2 0 0 1 0 6.4M17.5 14a6 6 0 0 1 3.5 6"/>',
        'stay' => '<path d="M4 20V8l8-5 8 5v12M2.5 20h19M10 20v-5h4v5M8 10h1.5M14.5 10H16"/>',
        'lake' => '<path d="M3 17c1.5-1 3-1 4.5 0s3 1 4.5 0 3-1 4.5 0 3 1 4.5 0M3 20.5c1.5-1 3-1 4.5 0s3 1 4.5 0 3-1 4.5 0 3 1 4.5 0M4 13l5-7 3.5 4.5L15 7l5 6"/>',
        'star' => '<path d="m12 3.5 2.5 5.2 5.7.8-4.1 4 1 5.6L12 16.4l-5.1 2.7 1-5.6-4.1-4 5.7-.8z"/>',
    ];
    $hlIcon = function (string $t): string {
        $t = mb_strtolower($t);
        foreach (['car' => ['car', 'drive', 'driving'], 'guide' => ['guide'], 'bus' => ['transfer', 'coach'], 'wine' => ['wine'], 'food' => ['food', 'farm', 'dining'],
            'music' => ['concert', 'opera', 'mozart', 'music'], 'users' => ['group'], 'stay' => ['stay', 'hotel'], 'lake' => ['lake', 'unhurried']] as $k => $words) {
            foreach ($words as $w) { if (str_contains($t, $w)) { return $k; } }
        }
        return 'star';
    };
    $usedPlaces = [];
    $highlights = collect($package->highlights ?? [])->map(function ($h) use (&$usedPlaces, $hlIcon) {
        $pls = collect(\App\Support\Places::inText($h))->reject(fn ($pl) => in_array($pl['key'], $usedPlaces, true));
        $pl = $pls->first(fn ($x) => ! empty($x['imageUrl'])) ?? $pls->first();
        if ($pl) { $usedPlaces[] = $pl['key']; }
        return (object) ['text' => $h, 'place' => $pl, 'icon' => $hlIcon($h)];
    });
    // Package inclusions: a thin-line icon per included item, picked from its wording.
    $ico += [
        'notes' => '<path d="M6 3h9l4 4v14H6z"/><path d="M15 3v4h4M9 11h7M9 14.5h7M9 18h4"/>',
        'cable' => '<path d="M3 4l18 4M12 6v3"/><rect x="7" y="9" width="10" height="9" rx="2"/><path d="M7 13h10M12 18v2.5"/>',
        'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
    ];
    $incIcon = function (string $t): string {
        $t = mb_strtolower($t);
        foreach (['cable' => ['cable car', 'gondola', 'ticket'], 'stay' => ['hotel', 'stay', 'breakfast', 'accommodation'], 'car' => ['car hire', 'rental car', 'car'], 'notes' => ['route notes', 'notes', 'suggestion'],
            'guide' => ['guide'], 'bus' => ['transfer', 'coach', 'transport'], 'compass' => ['sightseeing', 'tour', 'excursion'],
            'food' => ['dinner', 'lunch', 'meal'], 'wine' => ['wine', 'tasting'], 'music' => ['concert', 'opera']] as $k => $words) {
            foreach ($words as $w) { if (str_contains($t, $w)) { return $k; } }
        }
        return 'check';
    };
    // Places & map: a stylised Austria outline (simplified border, lon/lat) with the route plotted from the verified
    // coordinates in App\Support\Places. Inline SVG, so nothing loads from or links to another site.
    $proj = fn (float $lon, float $lat) => \App\Support\AustriaMap::project($lon, $lat);
    $austriaPath = \App\Support\AustriaMap::outline();
    $stops = $route->take(10)->values()->map(fn ($pl, $i) => ['n' => $i + 1, 'pl' => $pl, 'xy' => $proj($pl['lon'], $pl['lat'])]);
    $routePath = $stops->count() > 1 ? 'M'.$stops->map(fn ($s) => implode(' ', $s['xy']))->implode('L') : null;
    $svg = fn (string $k) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$ico[$k].'</svg>';
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
{{-- ===== HERO (unchanged): banner with the summary card, mobile facts bar ===== --}}
@include('pages.partials.banner')

<section class="factsbar" aria-label="Tour at a glance">
    <div class="wrap"><dl>
        <div><dt>Duration</dt><dd>{{ $days }} days &middot; {{ $package->nights }} nights</dd></div>
        @if($package->tourType)<div><dt>Style</dt><dd>{{ $package->tourType->name }}</dd></div>@endif
        @php $pkPlaces = collect(\App\Support\Places::forPackage($package))->pluck('name'); @endphp
        @if($pkPlaces->isNotEmpty())<div><dt>Places</dt><dd>{{ $pkPlaces->take(3)->implode(' · ') }}@if($pkPlaces->count() > 3) +{{ $pkPlaces->count() - 3 }}@endif</dd></div>@endif
        <div class="fprice"><dt>{{ $price ? 'From' : 'Price' }}</dt><dd>{{ $price ?: 'On request' }}@if($price)<small> per person</small>@endif</dd></div>
    </dl></div>
</section>

{{-- ===== ABOUT (only when the package has its own description; the hero already shows title, summary, price and facts) ===== --}}
@if($hasDesc)
<section class="sec about" id="overview">
    <div class="wrap narrow">
        <p class="pk-k c">About this journey</p>
        <div class="prose about-prose">{!! $package->description !!}</div>
    </div>
</section>
@endif

{{-- ===== PACKAGE ITINERARY (tabs) ===== --}}
@if(count($tabs))
<section class="sec pit" id="itinerary">
    <div class="wrap">
        <h2 class="pit-h">Package <em>itinerary</em></h2>
        @if($route->count() >= 2)
            <p class="pit-route"><span class="pit-route-k">Your route</span>@foreach($route->take(8) as $pl)<span>{{ $pl['name'] }}</span>@unless($loop->last)<i aria-hidden="true">&rarr;</i>@endunless @endforeach</p>
        @endif
        @if(count($tabs) > 1)
            <div class="ptabs" role="tablist" aria-label="Package information" data-ptabs hidden>
                @foreach($tabs as [$tid, $tl])<button type="button" role="tab" id="pt-{{ $tid }}" aria-controls="pp-{{ $tid }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" @unless($loop->first) tabindex="-1" @endunless>{{ $tl }}</button>@endforeach
            </div>
        @endif

        @if($package->itinerary)
        <div class="ppanel" id="pp-days" role="tabpanel" aria-labelledby="pt-days">
            <h3 class="ppanel-h">Itinerary details</h3>
            @foreach($package->itinerary as $day)
                @php
                    $dt = (string) ($day['title'] ?? '');
                    $label = 'Day '.$loop->iteration;
                    if (preg_match('/^\s*(Days?\s*\d+(?:\s*[-–—]\s*\d+)?)\s*[:\-–—]\s*(.+)$/iu', $dt, $m)) { $label = trim($m[1]); $dt = $m[2]; }
                    $dayPlaces = \App\Support\Places::inText($dt.' '.($day['body'] ?? ''));
                    $dayImg = collect($dayPlaces)->first(fn ($pl) => ! empty($pl['imageUrl']));
                @endphp
                <article @class(['dayrow', 'has-img' => $dayImg])>
                    <div class="dr-txt">
                        <p class="pk-k">{{ $label }}</p>
                        <h4>{{ $dt }}</h4>
                        <p>{{ $day['body'] ?? '' }}</p>
                    </div>
                    @if($dayImg)<figure class="dr-img"><img src="{{ $dayImg['imageUrl'] }}" alt="{{ $dayImg['name'] }}" loading="lazy"></figure>@endif
                </article>
            @endforeach
            <p class="pit-end"><a class="btn" {!! $quote() !!}>Request this itinerary <i aria-hidden="true">&rarr;</i></a><span>We can adjust any day to suit you.</span></p>
        </div>
        @endif

        @if($package->highlights)
        <div class="ppanel" id="pp-highlights" role="tabpanel" aria-labelledby="pt-highlights">
            <h3 class="ppanel-h">Tour highlights</h3>
            <div class="hlx">
                <div class="hlx-intro">
                    <p class="pk-k">The highlights</p>
                    <h4 class="hlx-h">Moments this journey <em>is built around</em></h4>
                    <p class="hlx-lede">{{ $highlights->count() }} highlights across {{ $days }} days. Every one can be adjusted, extended or swapped to suit the way you travel.</p>
                    <a class="btn" {!! $quote() !!}>Plan these highlights <i aria-hidden="true">&rarr;</i></a>
                </div>
                <ol class="hlx-list">
                    @foreach($highlights as $h)
                        <li>
                            <span class="hlx-n" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                            <div class="hlx-b">
                                <p class="hlx-meta">{{ $h->place ? $h->place['name'] : 'Included in your journey' }}@if($h->place && $h->place['region'] !== $h->place['name']) <span>{{ $h->place['region'] }}</span>@endif</p>
                                <h5>{{ $h->text }}</h5>
                                @if($h->place)<p class="hlx-fact">{{ $h->place['fact'] }}</p>@endif
                            </div>
                            @if($h->place && ! empty($h->place['imageUrl']))<img class="hlx-th" src="{{ $h->place['imageUrl'] }}" alt="{{ $h->place['name'] }}" loading="lazy">
                            @else<span class="hlx-ic">{!! $svg($h->icon) !!}</span>@endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
        @endif

        @if($package->inclusions || $package->exclusions)
        <div class="ppanel" id="pp-included" role="tabpanel" aria-labelledby="pt-included">
            <h3 class="ppanel-h">What is included</h3>
            <div class="hlx inc">
                <div class="hlx-intro">
                    <p class="pk-k">Package inclusions</p>
                    <h4 class="hlx-h">What your journey <em>includes</em></h4>
                    <p class="hlx-lede">Everything listed here is part of {{ $package->title }}. Anything not listed can be added when we tailor your quote.</p>
                    <p class="inc-price">@if($price)<small>From</small> <b>{{ $price }}</b> <small>per person</small>@else<b>Price on request</b>@endif</p>
                    <a class="btn" {!! $quote() !!}>Get a quote <i aria-hidden="true">&rarr;</i></a>
                </div>
                <div class="inc-lists">
                    @if($package->inclusions)
                        <p class="inc-k">Included</p>
                        <ul class="inc-yes">
                            @foreach($package->inclusions as $i)<li><span class="inc-ic">{!! $svg($incIcon($i)) !!}</span><span class="inc-t">{{ $i }}</span><span class="inc-ok" aria-hidden="true">{!! $svg('check') !!}</span></li>@endforeach
                        </ul>
                    @endif
                    @if($package->exclusions)
                        <p class="inc-k">Not included</p>
                        <ul class="inc-no">@foreach($package->exclusions as $i)<li>{{ $i }}</li>@endforeach</ul>
                        <p class="inc-ask">Need something added? <a {!! $quote(' data-interest="a question about what is included"') !!}>Ask us about it</a></p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(count($placesOn))
        <div class="ppanel" id="pp-places" role="tabpanel" aria-labelledby="pt-places">
            <h3 class="ppanel-h">Places on this journey</h3>
            <div class="hlx plx">
                <div class="hlx-intro">
                    <p class="pk-k">Places &amp; map</p>
                    <h4 class="hlx-h">Where this journey <em>takes you</em></h4>
                    <p class="hlx-lede">{{ $stops->count() }} places, numbered in the order you reach them.</p>
                    <figure class="plx-map">
                        <svg viewBox="{{ \App\Support\AustriaMap::viewBox() }}" role="img" aria-label="Map of Austria showing the route: {{ $stops->pluck('pl.name')->implode(', ') }}">
                            <path class="plx-land" d="{{ $austriaPath }}"/>
                            @if($routePath)<path class="plx-route" d="{{ $routePath }}"/>@endif
                            @foreach($stops as $s)
                                <g class="plx-pt" data-plx="{{ $s['n'] }}" transform="translate({{ $s['xy'][0] }} {{ $s['xy'][1] }})"><circle r="12"/><text y="4.4">{{ $s['n'] }}</text></g>
                            @endforeach
                        </svg>
                        <figcaption>Stylised map. Positions are approximate.</figcaption>
                    </figure>
                </div>
                <ol class="hlx-list plx-list">
                    @foreach($stops as $s)
                        @php $pl = $s['pl']; @endphp
                        <li data-plx="{{ $s['n'] }}">
                            <span class="hlx-n" aria-hidden="true">{{ sprintf('%02d', $s['n']) }}</span>
                            <div class="hlx-b">
                                @if($pl['region'] !== $pl['name'])<p class="hlx-meta">{{ $pl['region'] }}</p>@endif
                                <h5>{{ $pl['name'] }}</h5>
                                <p class="hlx-fact">{{ $pl['fact'] }}</p>
                                <a class="plx-more" href="{{ $pl['searchUrl'] }}">Journeys to {{ $pl['name'] }} <i aria-hidden="true">&rarr;</i></a>
                            </div>
                            @if(! empty($pl['imageUrl']))<img class="hlx-th" src="{{ $pl['imageUrl'] }}" alt="{{ $pl['name'] }}" loading="lazy">@else<span class="hlx-ic">{!! $svg('pin') !!}</span>@endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
        @endif
    </div>
</section>
@endif

{{-- ===== HOW IT WORKS ===== --}}
<section class="sec hiw">
    <div class="wrap">
        <div class="head"><p class="pk-k c">Simple and personal</p><h2 class="pit-h">How it <em>works</em></h2>
            <p class="hiw-lead">Three easy steps from first idea to your Austria journey, with one personal contact the whole way.</p></div>
        <ol class="hiw-grid">
            <li class="on"><span class="hiw-ic">{!! $svg('chat') !!}</span><small>Step 1</small><h3>Tell us your plans</h3><p>Share your dates and how you like to travel. It takes about a minute.</p></li>
            <li><span class="hiw-ic">{!! $svg('route') !!}</span><small>Step 2</small><h3>We shape your route</h3><p>A travel specialist adjusts this journey around you and sends a free quote.</p></li>
            <li><span class="hiw-ic">{!! $svg('sun') !!}</span><small>Step 3</small><h3>You enjoy Austria</h3><p>Hotels, routes and details are arranged, with one personal contact throughout.</p></li>
        </ol>
        <p class="hiw-cta"><a class="btn" href="#enquire">Start your enquiry <i aria-hidden="true">&darr;</i></a></p>
    </div>
</section>

{{-- ===== ENQUIRE NOW (the package form, same endpoint and rules as every form) ===== --}}
<section class="sec enq" id="enquire">
    <div class="wrap">
        <div class="enq-box">
            <h2 class="enq-h">Enquire <em>now</em></h2>
            <p class="enq-sub">Tell us your dates and group, and a travel specialist will tailor {{ $package->title }} for you, with no obligation.</p>
            @include('pages.partials.quote-form', ['formName' => 'package-quote', 'package' => $package, 'uid' => 'pkg-'.$package->id, 'button' => 'Send my enquiry', 'qfTrip' => true])
        </div>
    </div>
</section>

@if($stories->isNotEmpty())
<section class="sec tint" id="reviews">
    <div class="wrap">
        <div class="head"><p class="pk-k c">Guest stories</p><h2 class="pit-h">In their <em>own words</em></h2></div>
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
        <p class="faq-ask">Still have a question? <a {!! $quote(' data-interest="a question about '.e($package->title).'"') !!}>Ask a specialist</a></p>
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
    <a class="btn sm" {!! $quote() !!}>Get a free quote</a>
</div>
@endsection

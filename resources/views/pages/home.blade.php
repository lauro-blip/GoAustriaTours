@php
    // Home page. All data comes from the database; no invented facts.
    $img = fn (string $p) => asset('images/'.$p);
    $w = fn (string $n, string $suffix = '') => asset('images/winter/'.$n.$suffix.'.webp');
    $winter = ['hallstatt', 'zellamsee', 'kitzbuhel', 'steinernes-meer', 'tauplitz', 'untersberg', 'salzburg', 'schonbrunn', 'xmas-salzburg'];
    $places = function ($pkg) {
        return collect($pkg->itinerary ?? [])
            ->map(fn ($d) => trim(\Illuminate\Support\Str::after($d['title'] ?? '', ':')))
            ->flatMap(fn ($t) => preg_split('/\s+(?:and|&)\s+|,\s*/', $t))
            ->map(fn ($t) => \Illuminate\Support\Str::ucfirst(trim(preg_replace('/^the\s+/i', '', $t))))
            ->filter()->unique()->values();
    };
    $price = fn ($pkg) => $pkg->price_from ? '$'.number_format($pkg->price_from) : null;
    $phone = trim((string) ($site['phone'] ?? ''));
    $tel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $phone));
    $brand = $site['brand_name'] ?? 'GoAustriaTours';

    $pkgs = $packages->values()->map(function ($p, $i) use ($winter, $w, $places, $price) {
        return (object) [
            'p' => $p, 'image' => $w($winter[$i % count($winter)], '-768'), 'places' => $places($p), 'price' => $price($p),
            'dest' => $p->destination?->slug,
        ];
    });
    $featured = $pkgs->take(12)->values();
    $featuredPages = $featured->chunk(6);
    $popular = $pkgs->slice(6, 6)->values();

    $cols = [
        ['vienna-salzburg', 'Vienna, Salzburg & the Danube', 'Imperial elegance'],
        ['tyrol', 'Tyrol & the Alps', 'Peaks, lakes and snow'],
    ];
    $navPlaces = config('goaustria.nav_places', []);
    $tiles = [
        ['hallstatt', 'Salzkammergut', 'Hallstatt in Winter'],
        ['zellamsee', 'Salzburgerland', 'Zell am See & Kaprun'],
        ['kitzbuhel', 'Tyrol', 'Kitzbühel Old Town'],
        ['xmas-salzburg', 'Salzburg', 'Advent Markets'],
        ['untersberg', 'Salzburg', 'Untersberg Views'],
        ['tauplitz', 'Styria', 'Tauplitz & the Grimming'],
    ];
    $ico = [
        'cal' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4m8-4v4"/></svg>',
        'pin' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>',
        'route' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="19" r="2"/><circle cx="18" cy="5" r="2"/><path d="M8 19h7a3 3 0 0 0 0-6H9a3 3 0 0 1 0-6h7"/></svg>',
    ];
@endphp
@php
    $metaTitle = $activeTour ? $activeTour->name.' Tours of Austria | '.$brand : ($page?->meta_title ?: $brand);
    $metaDescription = $page?->meta_description ?: ($site['default_meta_description'] ?? '');
    $canonical = route('home');
    $ogImage = ! empty($site['default_social_image']) ? asset('storage/'.$site['default_social_image']) : $w('hero-wagrain', '-1280');
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $brand }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    {{-- Favicon: the Admin > Settings upload wins; otherwise the bundled "G" monogram in public/ --}}
    @if(! empty($site['favicon']))
        <link rel="icon" href="{{ asset('storage/'.$site['favicon']) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192.png') }}">
    @endif
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @include('partials.tracking-head')
    <link rel="preload" as="image" href="{{ $w('hero-wagrain', '-1280') }}" fetchpriority="high">
    @vite(['resources/js/home.js'])
    <script type="application/ld+json">{!! json_encode(\App\Support\Schema::organization($site), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
</head>
<body class="t3" style="margin:0">
@if(! empty($site['gtm_id']))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $site['gtm_id'] }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
@endif
<a href="#main" class="skip">Skip to content</a>

{{-- ============ HEADER ============ --}}
<header class="hdr">
    <div class="wrap">
        <a class="logo" href="{{ route('home') }}" aria-label="{{ $brand }}"><img class="on-dark" src="{{ $img('logo-white.webp') }}" alt="{{ $brand }}" width="500" height="167"><img class="on-light" src="{{ $img('logo-navy.webp') }}" alt="" width="500" height="167"></a>
        <ul class="nav" aria-label="Main">
            <li><a class="on" href="{{ route('home') }}">Home</a></li>
            <li><button type="button">Destinations <i class="caret"></i></button>
                <div class="mega">
                    <div class="mega-in">
                        <div>
                            <h5>Explore by region</h5>
                            <div class="mega-cards">
                                @foreach($destinations as $d)
                                    <a href="{{ route('packages.index', ['destination' => $d->slug]) }}"><img src="{{ $w($winter[$loop->index % count($winter)], '-768') }}" alt="" loading="lazy"><span>{{ $d->name }}</span></a>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h5>Popular destinations</h5>
                            <ul class="mega-list">
                                @foreach($navPlaces as $slug => $list)@foreach($list as $pl)<li><a href="{{ route('packages.index', ['q' => $pl[1]]) }}">{{ $pl[0] }}</a></li>@endforeach @endforeach
                            </ul>
                            <a class="more" href="{{ route('packages.index') }}">View all destinations &rarr;</a>
                        </div>
                    </div>
                </div>
            </li>
            <li><button type="button">Austria Vacations <i class="caret"></i></button>
                <div class="mega">
                    <div class="mega-in one">
                        <div>
                            <h5>Tour types</h5>
                            <div class="mega-cards c3">
                                @foreach($tourTypes as $t)
                                    <a href="{{ route('tours.show', $t->slug) }}"><img src="{{ $w($winter[($loop->index + 2) % count($winter)], '-768') }}" alt="" loading="lazy"><span>{{ $t->name }}</span></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li><button type="button">Travel Style <i class="caret"></i></button>
                <div class="mega">
                    <div class="mega-in one">
                        <div>
                            <h5>Travel style</h5>
                            <div class="mega-cards c3">
                                @foreach(config('goaustria.travel_styles', []) as $slug => $st)
                                    <a class="txt" href="{{ route('packages.index', ['style' => $slug]) }}"><b>{{ $st['name'] }}</b><em>{{ $st['text'] }}</em><u>Discover</u></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li><button type="button">About Us <i class="caret"></i></button>
                <div class="drop">
                    <a href="{{ route('about') }}">About us</a>
                    <a href="{{ route('journal.index') }}">Journal</a>
                    <a href="{{ route('testimonials') }}">Testimonials</a>
                    <a href="{{ route('terms') }}">Terms &amp; Conditions</a>
                    <a href="{{ route('privacy') }}">Privacy Policy</a>
                </div>
            </li>
            <li><a href="{{ route('contact') }}">Contact us</a></li>
        </ul>
        <a class="cta" href="{{ route('contact') }}" data-quote>Plan your journey <i class="caret"></i></a>
        <button class="burger" type="button" data-open-menu aria-label="Open menu"><i></i><i></i><i></i></button>
    </div>
</header>
<nav class="mobile" aria-label="Mobile">
    <button class="close" type="button" data-close-menu aria-label="Close menu">&times;</button>
    <p class="mt">Plan your journey</p>
    <a href="{{ route('home') }}" data-close-menu>Home</a>
    <div class="sub"><a href="{{ route('packages.index') }}">Destinations</a>@foreach($destinations as $d)<a href="{{ route('packages.index', ['destination' => $d->slug]) }}">{{ $d->name }}</a>@endforeach</div>
    <div class="sub"><a href="{{ route('packages.index') }}">Austria Vacations</a>@foreach($tourTypes as $t)<a href="{{ route('tours.show', $t->slug) }}">{{ $t->name }}</a>@endforeach</div>
    <a href="{{ route('about') }}">About us</a>
    <a href="{{ route('contact') }}">Contact us</a>
</nav>

<main id="main">
{{-- ============ HERO ============ --}}
<section class="hero">
    <div class="hero-bg" aria-hidden="true">
        @foreach(['hero-wagrain', 'hero-salzburg', 'hero-montafon'] as $h)
            <img src="{{ $w($h, '-1280') }}" srcset="{{ $w($h, '-768') }} 768w, {{ $w($h, '-1280') }} 1280w, {{ $w($h) }} 1920w" sizes="100vw" alt="" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif>
        @endforeach
    </div>
    <div class="wrap hero-in">
        <div class="hero-copy">
            <p class="eyebrow">{{ $activeTour ? $activeTour->name.' tours of Austria' : 'Self drive · Escorted · Coach tours' }}</p>
            <h1>Your Austria Trip,<em>Planned for You</em></h1>
            <p class="sub">@if($activeTour && filled($activeTour->tagline)){{ $activeTour->tagline }}@else Answer a few quick questions about how you like to travel, and a personal travel specialist will design your Austria itinerary.@endif</p>
        </div>
        <div class="hero-quiz">
            <span class="qtag">Complimentary &middot; No obligation</span>
            <p class="qtitle">Plan my journey</p>
            @include('pages.partials.study-quiz')
        </div>
    </div>
    <div class="strip" aria-label="Where to?">
        <b>Where to?</b>
        <div class="mq"><div class="mq-track">
            @foreach([1, 2] as $copy)
                <div class="mq-set" @if($copy === 2) aria-hidden="true" @endif>@foreach($navPlaces as $list)@foreach($list as $pl)<a href="{{ route('packages.index', ['q' => $pl[1]]) }}" @if($copy === 2) tabindex="-1" @endif>{{ $pl[1] }}</a>@endforeach @endforeach</div>
            @endforeach
        </div></div>
    </div>
</section>

{{-- ============ TRUST STRIP: built only from Admin > Trust & tracking; blank or "[CONFIRM" values never show ============ --}}
@php
    $marks = collect(range(1, 4))->map(fn ($n) => [
        'label' => trim((string) ($site["trust_mark_{$n}_label"] ?? '')),
        'logo' => trim((string) ($site["trust_mark_{$n}_logo"] ?? '')),
        'url' => trim((string) ($site["trust_mark_{$n}_url"] ?? '')),
    ])->filter(fn ($m) => $m['label'] !== '' || $m['logo'] !== '')->values();
    $established = trim((string) ($site['established_line'] ?? ''));
    $reviewText = trim((string) ($site['review_summary'] ?? ''));
    $reviewUrl = trim((string) ($site['review_url'] ?? ''));
@endphp
@if($marks->isNotEmpty() || $established !== '' || $reviewText !== '')
<section class="trust" aria-label="Why you can trust us">
    <div class="wrap trust-in">
        @if($established !== '')<p class="trust-est">{{ $established }}</p>@endif
        @if($marks->isNotEmpty())
            <ul class="trust-marks" aria-label="Memberships and protection">
                @foreach($marks as $m)
                    <li>
                        @if($m['url'] !== '')<a href="{{ $m['url'] }}" target="_blank" rel="noopener">@endif
                        @if($m['logo'] !== '')<img src="{{ asset('storage/'.$m['logo']) }}" alt="{{ $m['label'] ?: 'Membership logo' }}" loading="lazy">@else<span>{{ $m['label'] }}</span>@endif
                        @if($m['url'] !== '')</a>@endif
                    </li>
                @endforeach
            </ul>
        @endif
        @if($reviewText !== '')<p class="trust-rev">@if($reviewUrl !== '')<a href="{{ $reviewUrl }}" target="_blank" rel="noopener">{{ $reviewText }}</a>@else{{ $reviewText }}@endif</p>@endif
    </div>
</section>
@endif

{{-- ============ PLANNED AROUND YOU BAND ============ --}}
<section class="band" aria-label="How we plan your Austria journey">
    <div class="wrap band-grid">
        <div class="bi rv"><span class="bic"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="24" cy="24" r="19"/><circle cx="24" cy="24" r="1.5"/><path d="M31 17l-4.5 9.5L17 31l4.5-9.5z"/><path d="M24 3v4M24 41v4M3 24h4M41 24h4"/></svg></span><h3>Custom Itineraries</h3><p>Self drive, escorted or by coach: every route is tailored to your dates, pace and interests.</p></div>
        <div class="bi rv"><span class="bic"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 42V15l16-8 16 8v27"/><path d="M4 42h40"/><path d="M19 42V31h10v11"/><path d="M15 21h4M29 21h4M15 26h4M29 26h4"/><path d="M24 2.5v4"/></svg></span><h3>Handpicked Stays</h3><p>Hotels chosen for their location and character, with the insight of local experts.</p></div>
        <div class="bi rv"><span class="bic"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 33a16 16 0 0 1 32 0z"/><path d="M5 38h38"/><path d="M24 13v4"/><path d="M20.5 12h7"/><path d="M13 33a11 11 0 0 1 6-9.5"/></svg></span><h3>Personal Contact</h3><p>A real person who knows your plans and answers your questions before and during your trip.</p></div>
        <div class="bi rv"><span class="bic"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M24 5v38"/><path d="M24 9h13l4 4-4 4H24z"/><path d="M24 21H11l-4 4 4 4h13z"/><path d="M24 33h11l3 3-3 3H24"/><path d="M18 43h12"/></svg></span><h3>Three Ways to Travel</h3><p>Self drive, escorted or coach. Choose the style that suits you and we plan the rest.</p></div>
    </div>
</section>

{{-- ============ WHY TRAVEL WITH US ============ --}}
<section class="sec why">
    <div class="wrap why-grid">
        <div class="why-media rv">
            <figure class="why-main"><img src="{{ $w('schonbrunn', '-1280') }}" alt="Schönbrunn Palace and its gardens under fresh snow" loading="lazy"></figure>
            <figure class="why-inset"><img src="{{ $w('kitzbuhel', '-768') }}" alt="A snowy Tyrolean old town street with horse-drawn carriages" loading="lazy"></figure>
            <p class="why-badge"><b>Self drive</b><b>Escorted</b><b>Coach</b><span>Tours of Austria, tailored to you</span></p>
        </div>
        <div class="why-copy rv">
            <p class="eyebrow dark">Why travel with {{ $brand }}</p>
            <h2 class="h">Austria, <em>planned with care</em></h2>
            <p class="lede">From snow-dusted palaces to lakeside villages and Alpine peaks, we shape every journey around the way you want to travel.</p>
            <p class="why-p">Whether you would rather take the wheel, travel with a guide or sit back on a comfortable coach, we plan the route, the hotels and the pace together with you. Nothing is fixed until you are happy with it.</p>
            <p class="why-p">Tell us your dates, your interests and how you like to travel, and a personal travel specialist will come back with an itinerary built around you, with no obligation.</p>
            <div class="why-cta"><a class="btn" href="{{ route('contact') }}" data-quote>Get a free quote <i aria-hidden="true">&rarr;</i></a></div>
        </div>
    </div>
</section>

{{-- ============ TOP-RATED TOURS (filter + pager) ============ --}}
<section class="sec tint" id="tours" data-tours>
    <div class="wrap">
        <div class="tours-head rv">
            <div>
                <p class="eyebrow dark">Handpicked journeys</p>
                <h2 class="h">Our Top-Rated <em>Austrian Tours</em></h2>
                <p class="lede">Hand-built journeys through the cities, lakes and Alps, in every season.</p>
            </div>
            <div class="tours-filter">
                <div class="chips" role="group" aria-label="Filter by destination">
                    <button type="button" class="chip on" data-dest="">All packages</button>
                    @foreach($destinations as $d)<button type="button" class="chip" data-dest="{{ $d->slug }}">{{ $d->name }}</button>@endforeach
                </div>
                <p class="count" data-count aria-live="polite">Showing 1&ndash;{{ min(6, $featured->count()) }} of {{ $featured->count() }} tours</p>
            </div>
        </div>
        <div class="grid" data-grid>
            @foreach($featured as $f)
                <div class="cell" data-dest="{{ $f->dest }}">@include('pages.partials.package-card', ['f' => $f])</div>
            @endforeach
        </div>
        <nav class="pager" data-pager aria-label="Pagination"></nav>
        <div class="tours-cta"><a class="btn" href="{{ route('contact') }}" data-quote>Get a free quote <i aria-hidden="true">&rarr;</i></a><a class="link" href="{{ route('packages.index') }}">View all packages</a></div>
    </div>
</section>

{{-- ============ THE DIFFERENCE (real counts only) ============ --}}
<section class="sec diff">
    <div class="diff-bg" aria-hidden="true"><img src="{{ $w('steinernes-meer', '-1280') }}" alt="" loading="lazy"></div>
    <div class="wrap diff-grid">
        <div class="diff-copy rv">
            <p class="eyebrow light">The {{ $brand }} difference</p>
            <h2 class="h light">Why travel <em>with us?</em></h2>
            <p class="lede light">We plan Austria for travellers who would rather be looked after than left to work it out alone.</p>
            <ul class="diff-points">
                <li>Routes shaped around your dates, pace and interests</li>
                <li>Handpicked hotels and local Austria expertise</li>
                <li>One personal contact from first enquiry to your return home</li>
            </ul>
            <div class="diff-cta"><a class="btn" href="{{ route('contact') }}" data-quote>Get a free quote <i aria-hidden="true">&rarr;</i></a><a class="link" href="{{ route('about') }}">Meet the team</a></div>
        </div>
        <div class="stats rv">
            <div class="stat"><b data-n="{{ $packages->count() }}">0</b><div><strong>Journeys</strong><span>Itineraries to choose from, each open to change before you book.</span></div></div>
            <div class="stat"><b data-n="{{ $destinations->count() }}">0</b><div><strong>Regions of Austria</strong><span>From imperial Vienna to the high peaks of Tyrol.</span></div></div>
            <div class="stat"><b data-n="{{ $tourTypes->count() }}">0</b><div><strong>Ways to travel</strong><span>Self drive, escorted or by coach.</span></div></div>
        </div>
    </div>
</section>

{{-- ============ TOP DESTINATIONS ============ --}}
@php
    $destImg = ['vienna-salzburg' => 'hero-salzburg', 'tyrol' => 'hero-montafon'];
    $destTag = ['vienna-salzburg' => 'Imperial palaces and Baroque music', 'tyrol' => 'Alpine valleys, lakes and peaks'];
    $destSixth = ['vienna-salzburg' => 'Melk', 'tyrol' => 'Achensee'];   // both occur in real package itineraries
    $bespokePlaces = ['Vienna', 'Salzburg', 'Hallstatt', 'Innsbruck', 'Zell am See', 'Graz'];
@endphp
<section class="sec" id="destinations">
    <div class="wrap">
        <div class="head rv">
            <p class="eyebrow dark">Where to go</p>
            <h2 class="h">Explore Top <em>Destinations</em></h2>
            <p class="lede">The imperial cities, the high Alps, or a route entirely your own.</p>
        </div>
        <div class="dgrid three">
            @foreach($destinations->take(6) as $d)
                @php
                    $pl = collect($navPlaces[$d->slug] ?? [])->map(fn ($x) => $x[1])->when(isset($destSixth[$d->slug]), fn ($c) => $c->push($destSixth[$d->slug]))->unique()->take(6);
                    $tagline = $destTag[$d->slug] ?? \Illuminate\Support\Str::limit((string) $d->summary, 38);
                    $n = $pkgs->where('dest', $d->slug)->count();
                    $di = $destImg[$d->slug] ?? $winter[($loop->index * 2) % count($winter)];
                @endphp
                <article class="dtile rv">
                    <div class="dt-photo">
                        <img src="{{ $w($di, '-1280') }}" alt="" loading="lazy">
                        @if($n)<p class="dt-kick">{{ $n }} {{ $n === 1 ? 'journey' : 'journeys' }}</p>@endif
                        <div class="dt-title">
                            <h3>{{ $d->name }}</h3>
                            @if($tagline)<p>{{ $tagline }}</p>@endif
                        </div>
                    </div>
                    <div class="dt-body">
                        @if($pl->isNotEmpty())
                            <ul class="dt-places">@foreach($pl as $x)<li><a href="{{ route('packages.index', ['q' => $x]) }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ $x }}</a></li>@endforeach</ul>
                        @endif
                        <div class="dt-foot">
                            <a class="dt-explore" href="{{ route('packages.index', ['destination' => $d->slug]) }}">Explore</a>
                            <a class="btn sm" href="{{ route('contact') }}" data-quote data-interest="{{ $d->name }}">Enquire now</a>
                        </div>
                    </div>
                </article>
            @endforeach
            @if($destinations->take(6)->count() < 3)
                <article class="dtile rv">
                    <div class="dt-photo">
                        <img src="{{ $w('hallstatt', '-1280') }}" alt="" loading="lazy">
                        <p class="dt-kick">Bespoke</p>
                        <div class="dt-title">
                            <h3>Your Own Route</h3>
                            <p>Mix any of these. Tell us your plan.</p>
                        </div>
                    </div>
                    <div class="dt-body">
                        <ul class="dt-places">@foreach($bespokePlaces as $x)<li><a href="{{ route('packages.index', ['q' => $x]) }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ $x }}</a></li>@endforeach</ul>
                        <div class="dt-foot">
                            <a class="dt-explore" href="{{ route('packages.index') }}">Explore</a>
                            <a class="btn sm" href="{{ route('contact') }}" data-quote data-interest="a custom route through Austria">Plan my route</a>
                        </div>
                    </div>
                </article>
            @endif
        </div>
        <p class="dest-foot"><a class="link" href="{{ route('packages.index') }}">View all destinations &rarr;</a></p>
    </div>
</section>

{{-- ============ POPULAR PACKAGES ============ --}}
@if($popular->isNotEmpty())
<section class="sec tint">
    <div class="wrap">
        <div class="head rv">
            <p class="eyebrow dark">Handpicked itineraries</p>
            <h2 class="h oneline">Austria, <em>Perfectly Planned</em></h2>
            <p class="lede">More of our most-loved itineraries, each one open to change before you book.</p>
        </div>
        <div class="grid">@foreach($popular as $f)<div class="cell">@include('pages.partials.package-card', ['f' => $f])</div>@endforeach</div>
    </div>
</section>
@endif

{{-- ============ CTA ============ --}}
<section class="sec cta2" id="plan">
    <div class="cta2-bg" aria-hidden="true"><img src="{{ $w('hero-montafon', '-1280') }}" alt="" loading="lazy"></div>
    <div class="wrap center rv">
        <p class="eyebrow light">Complimentary &middot; No obligation</p>
        <h2 class="h light">Tell Us Your Dream. <em>We Will Plan the Rest.</em></h2>
        <p class="lede light">Share when you would like to travel and how you like to see a country, and a personal travel specialist will design your Austria itinerary.</p>
        <div class="cta2-btns">
            <a class="btn" href="{{ route('contact') }}" data-quote>Plan my journey <i aria-hidden="true">&rarr;</i></a>
            @if($phone)<a class="cta2-call" href="tel:{{ $tel }}">Or call {{ $phone }}</a>@endif
        </div>
    </div>
</section>

{{-- ============ TRAVEL YOUR WAY ============ --}}
<section class="sec" id="ways">
    <div class="wrap">
        <div class="head rv">
            <p class="eyebrow dark">Choose your style</p>
            <h2 class="h oneline">Your Journey. <em>Your Way.</em></h2>
            <p class="lede">Drive at your own pace, travel with a guide, or ride in comfort. Every style is planned around you.</p>
        </div>
        @php $wayImg = ['self-drive' => 'way-self-drive', 'escorted' => 'way-escorted', 'coach' => 'way-coach']; @endphp
        <div class="ways">
            @foreach($tourTypes->take(3) as $t)
                @php $tn = $pkgs->filter(fn ($f) => $f->p->tourType?->id === $t->id)->count(); @endphp
                <a class="way rv" href="{{ route('tours.show', $t->slug) }}">
                    <img src="{{ $w($wayImg[$t->slug] ?? $winter[($loop->index + 3) % count($winter)], '-768') }}" alt="" loading="lazy">
                    @if($tn)<span class="wcount">{{ $tn }} {{ $tn === 1 ? 'journey' : 'journeys' }}</span>@endif
                    <div class="wbody">
                        <h3>{{ $t->name }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($t->tagline ?: $t->summary, 80) }}</p>
                        <span class="wgo">Explore {{ $t->name }} <i aria-hidden="true">&rarr;</i></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ THE COLLECTION ============ --}}
@php
    $gallery = [
        ['hallstatt', 'Salzkammergut', 'Hallstatt in Winter', 'g-a'],
        ['zellamsee', 'Salzburgerland', 'Zell am See & Kaprun', 'g-b'],
        ['xmas-salzburg', 'Salzburg', 'Advent Markets', 'g-c'],
        ['kitzbuhel', 'Tyrol', 'Kitzbühel Old Town', 'g-d'],
        ['untersberg', 'Salzburg', 'Untersberg Views', 'g-e'],
        ['tauplitz', 'Styria', 'Tauplitz & the Grimming', 'g-f'],
        ['schonbrunn', 'Vienna', 'Schönbrunn Palace', 'g-g'],
    ];
@endphp
<section class="sec tint" id="gallery">
    <div class="wrap">
        <div class="head rv">
            <p class="eyebrow dark">The Austria gallery</p>
            <h2 class="h">Austria&rsquo;s Finest Places, <em>Beautifully Curated</em></h2>
            <p class="lede">Snow-covered villages, frozen lakes and high Alpine views: the places our guests ask for first.</p>
        </div>
        <div class="gal">
            @foreach($gallery as $g)
                <a class="gi {{ $g[3] }} rv" href="{{ route('packages.index', ['q' => explode(' ', $g[2])[0]]) }}">
                    <img src="{{ $w($g[0], '-1280') }}" alt="{{ $g[2] }}, {{ $g[1] }}" loading="lazy">
                    <span class="gl">{{ $g[1] }}</span>
                    <div class="gt"><h3>{{ $g[2] }}</h3><i aria-hidden="true">&rarr;</i></div>
                </a>
            @endforeach
        </div>
        <p class="center"><a class="btn" href="{{ route('contact') }}" data-quote>Plan my journey <i aria-hidden="true">&rarr;</i></a></p>
    </div>
</section>

{{-- ============ TESTIMONIALS ============ --}}
@if($testimonials->isNotEmpty())
<section class="sec" id="stories">
    <div class="wrap">
        <div class="head rv">
            <p class="eyebrow dark">Guest reviews</p>
            <h2 class="h oneline">Guest <em>Stories</em></h2>
            <p class="lede">Words from travellers who let us shape their trip to Austria.</p>
        </div>
        <div class="reviews tcards" data-reviews>
            <div class="rtrack">
                @foreach($testimonials as $t)
                    @php $ini = collect(preg_split('/\s+/', trim($t->name)))->filter()->take(2)->map(fn ($x) => mb_strtoupper(mb_substr($x, 0, 1)))->implode(''); @endphp
                    <figure class="rev">
                        <span class="qi" aria-hidden="true"><svg viewBox="0 0 48 36" fill="currentColor"><path d="M0 36V19C0 8 6 1.5 17 0l1.6 5C13 6.6 10.5 10 10.5 15H19v21H0zm27 0V19C27 8 33 1.5 44 0l1.600 5C40 6.600 37.500 10 37.500 15H46v21H27z"/></svg></span>
                        <blockquote>{{ $t->quote }}</blockquote>
                        <figcaption><span class="av" aria-hidden="true">{{ $ini }}</span><span class="who"><b>{{ $t->name }}</b>@if($t->location)<small>{{ $t->location }}</small>@endif</span></figcaption>
                    </figure>
                @endforeach
            </div>
            @if($testimonials->count() > 3)
                <div class="rnav"><button type="button" data-prev aria-label="Previous story">&larr;</button><button type="button" data-next aria-label="Next story">&rarr;</button></div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- ============ MOMENTS ============ --}}
<section class="sec tint" id="moments">
    <div class="wrap">
        <div class="head rv">
            <p class="eyebrow dark">Featured journeys</p>
            <h2 class="h oneline">Moments Worth <em>Travelling For</em></h2>
            <p class="lede">Journeys built around the places you will remember longest.</p>
        </div>
        <div class="mom">
            @foreach($pkgs->take(6) as $f)
                <article class="mc rv">
                    <img src="{{ $w($winter[($loop->index + 4) % count($winter)], '-768') }}" alt="" loading="lazy">
                    @if($f->p->tourType)<span class="mt">{{ $f->p->tourType->name }}</span>@endif
                    <div class="mb">
                        @if($f->p->destination)<p class="mk">{{ $f->p->destination->name }}</p>@endif
                        <h3><a href="{{ route('packages.show', $f->p) }}">{{ $f->p->title }}</a></h3>
                        <p class="mm">@if($f->p->nights){{ $f->p->nights }} nights<span aria-hidden="true"> &middot; </span>@endif{{ $f->price ? 'From '.$f->price : 'Price on request' }}</p>
                        <div class="mact">
                            <a class="mview" href="{{ route('packages.show', $f->p) }}">View journey</a>
                            <a class="btn sm" href="{{ route('packages.show', $f->p) }}#quote" data-quote data-package-id="{{ $f->p->id }}" data-package="{{ $f->p->title }}">Enquire</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ JOURNAL ============ --}}
@if($posts->isNotEmpty())
<section class="sec" id="journal">
    <div class="wrap">
        <div class="head rv">
            <p class="eyebrow dark">Stories and tips</p>
            <h2 class="h oneline">Austria Travel <em>Journal</em></h2>
            <p class="lede">Ideas, advice and winter inspiration for planning your trip.</p>
        </div>
        <div class="jr">
            @foreach($posts as $post)
                <article class="jc rv">
                    <a class="jimg" href="{{ route('journal.show', $post) }}" tabindex="-1" aria-hidden="true"><img src="{{ $post->imageUrl() }}" alt="{{ $post->image_alt }}" loading="lazy" width="800" height="600"></a>
                    <div class="jb">
                        <p class="jmeta">@if($post->category)<span>{{ $post->category }}</span><i aria-hidden="true">&middot;</i>@endif<time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M j, Y') }}</time><i aria-hidden="true">&middot;</i><span>{{ $post->readingMinutes() }} min read</span></p>
                        <h3><a href="{{ route('journal.show', $post) }}">{{ $post->title }}</a></h3>
                        <p class="jx">{{ $post->excerpt }}</p>
                        <a class="jmore" href="{{ route('journal.show', $post) }}">Read article <i aria-hidden="true">&rarr;</i></a>
                    </div>
                </article>
            @endforeach
        </div>
        <p class="center"><a class="btn" href="{{ route('journal.index') }}">Read the journal <i aria-hidden="true">&rarr;</i></a></p>
    </div>
</section>
@endif

</main>

{{-- ============ FOOTER ============ --}}
<section class="fcta">
    <div class="wrap center">
        <p class="fcta-t">Ready to Plan Your Perfect Austria Journey?</p>
        <a class="btn" href="{{ route('contact') }}" data-quote>Get your free quote <i aria-hidden="true">&rarr;</i></a>
        <p class="fcta-s">Need help deciding? Speak directly with our Austria travel specialists.</p>
        @if($phone)<a class="fcta-p" href="tel:{{ $tel }}">{{ $phone }}</a>@endif
    </div>
</section>
<footer class="foot">
    <div class="wrap foot-grid">
        <div class="fc fc-contact">
            @if($phone)<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg></span><div><h4>Call Now</h4><a href="tel:{{ $tel }}">{{ $phone }}</a></div></div>@endif
            @if(! empty($site['email']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><div><h4>Email</h4><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></div></div>@endif
            @if(! empty($site['address']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></span><div><h4>Address</h4><p>{!! nl2br(e($site['address'])) !!}</p></div></div>@endif
            @if(! $phone && empty($site['email']) && empty($site['address']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><div><h4>Get in touch</h4><a href="{{ route('contact') }}" data-quote>Send us an enquiry</a></div></div>@endif
        </div>
        <div class="fc"><h4>Destinations</h4>@foreach($destinations as $d)<a href="{{ route('packages.index', ['destination' => $d->slug]) }}">{{ $d->name }}</a>@endforeach<a href="{{ route('packages.index') }}">All packages</a></div>
        <div class="fc"><h4>Travel Styles</h4>@foreach(config('goaustria.travel_styles', []) as $slug => $st)<a href="{{ route('packages.index', ['style' => $slug]) }}">{{ $st['name'] }}</a>@endforeach</div>
        <div class="fc fc-brand">
            <img src="{{ $img('logo-white.webp') }}" alt="{{ $brand }}" width="500" height="167" loading="lazy">
            <p>Thoughtfully crafted Austria journeys by self drive, escorted tour and coach. Discover Austria beautifully with {{ $brand }}.</p>
        </div>
    </div>
    <div class="foot-bar">
        <div class="wrap foot-bar-in">
            <p class="copy">&copy; {{ date('Y') }} {{ $brand }}. All rights reserved.</p>
            <nav class="foot-legal" aria-label="Company"><a href="{{ route('about') }}">About us</a><a href="{{ route('journal.index') }}">Journal</a><a href="{{ route('contact') }}">Contact us</a><a href="{{ route('terms') }}">Terms &amp; Conditions</a><a href="{{ route('privacy') }}">Privacy Policy</a><a href="{{ route('photo-credits') }}">Photo credits</a></nav>
            <div class="foot-soc">
                @if(! empty($site['social_facebook']))<a href="{{ $site['social_facebook'] }}" rel="noopener" target="_blank" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.5H16l.5-3h-3V8.6c0-.9.3-1.5 1.6-1.5H16.6V4.4A20 20 0 0 0 14.3 4.3c-2.3 0-3.8 1.4-3.8 3.9v2.3H8v3h2.5V21h3z"/></svg></a>@endif
                @if(! empty($site['social_instagram']))<a href="{{ $site['social_instagram'] }}" rel="noopener" target="_blank" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.6"/><circle cx="16.8" cy="7.2" r="0.8" fill="currentColor"/></svg></a>@endif
                @if(! empty($site['social_youtube']))<a href="{{ $site['social_youtube'] }}" rel="noopener" target="_blank" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3L10 15z"/></svg></a>@endif
            </div>
        </div>
    </div>
</footer>

@include('pages.partials.study-modal')

{{-- sticky consult bar --}}
<div class="consult">
    <p>@if($phone && ! str_contains($phone, '[CONFIRM'))Consult with our Travel Specialists at <a href="tel:{{ $tel }}">{{ $phone }}</a>@else Talk to our Austria travel specialists @endif</p>
    <a class="rq" href="{{ route('contact') }}" data-quote>Request a quote <i aria-hidden="true">&rarr;</i></a>
</div>
</body>
</html>

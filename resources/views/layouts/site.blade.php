@php
    $metaTitle = $metaTitle ?? $brand;
    $metaDescription = $metaDescription ?? ($site['default_meta_description'] ?? '');
    $canonical = $canonical ?? url()->current();
    $noindex = $noindex ?? false;
    $ogImage = $ogImage ?? (! empty($site['default_social_image']) ? asset('storage/'.$site['default_social_image']) : $w('hero-wagrain', '-1280'));
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonical }}">
    @if($noindex)<meta name="robots" content="noindex, nofollow">@endif
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
    @stack('head')
    @include('partials.tracking-head')
    @stack('tracking')
    @stack('preload')
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/inter-latin-wght-normal.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/playfair-display-latin-wght-normal.woff2') }}" crossorigin>
    @vite(['resources/js/home.js'])
    @unless(request()->routeIs('home'))@vite(['resources/css/inner.css'])@endunless
    <script type="application/ld+json">{!! json_encode(\App\Support\Schema::organization($site), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @stack('jsonld')
</head>
<body class="t3" style="margin:0">
@if(! empty($site['gtm_id']))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $site['gtm_id'] }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
@endif
<a href="#main" class="skip">Skip to content</a>

{{-- ============ HEADER ============ --}}
<header class="hdr">
    <div class="wrap">
        <a class="logo" href="{{ route('home') }}" aria-label="{{ $brand }}"><img class="on-dark" src="{{ $img('logo-white.webp') }}" alt="{{ $brand }}" width="500" height="167"><img class="on-light" loading="lazy" src="{{ $img('logo-navy.webp') }}" alt="" width="500" height="167"></a>
        <ul class="nav" aria-label="Main">
            <li><a @class(['on' => request()->routeIs('home')]) href="{{ route('home') }}">Home</a></li>
            <li><button type="button">Destinations <i class="caret"></i></button>
                <div class="mega">
                    <div class="mega-in">
                        <div>
                            <h5>Explore by region</h5>
                            <div class="mega-cards">
                                @foreach($destinations as $d)
                                    @php $dn = $pkgCounts['dest'][$d->slug] ?? 0; @endphp
                                    <a href="{{ route('packages.index', ['destination' => $d->slug]) }}"><img src="{{ $w($winter[$loop->index % count($winter)], '-768') }}" alt="" loading="lazy"><span><b>{{ $d->name }}</b>@if($dn)<small>{{ $dn }} {{ $dn === 1 ? 'journey' : 'journeys' }}</small>@endif</span></a>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h5>Popular places</h5>
                            <ul class="mega-list">
                                @foreach($navPlaces as $slug => $list)@foreach($list as $pl)<li><a href="{{ route('packages.index', ['q' => $pl[1]]) }}">{{ $pl[0] }}</a></li>@endforeach @endforeach
                            </ul>
                            <a class="more" href="{{ route('packages.index') }}">View all journeys &rarr;</a>
                        </div>
                        <aside class="mega-promo">
                        <p class="mp-k">Personal planning</p>
                        <h5>Not sure where to start?</h5>
                        <p>Answer a few quick questions and a travel specialist will plan your route.</p>
                        <a class="btn sm" href="{{ route('contact') }}" data-quote>Plan my journey <i aria-hidden="true">&rarr;</i></a>
                    </aside>
                    </div>
                </div>
            </li>
            <li><button type="button">Austria Vacations <i class="caret"></i></button>
                <div class="mega">
                    <div class="mega-in tt">
                        <div>
                            <h5>Choose how you travel</h5>
                            <div class="mega-cards c3">
                                @foreach($tourTypes as $t)
                                    @php $tn = $pkgCounts['type'][$t->id] ?? 0; @endphp
                                    <a href="{{ route('tours.show', $t->slug) }}"><img src="{{ $w($wayImg[$t->slug] ?? $winter[($loop->index + 2) % count($winter)], '-768') }}" alt="" loading="lazy"><span><b>{{ $t->name }}</b><small>{{ $tn ? $tn.' '.($tn === 1 ? 'journey' : 'journeys') : \Illuminate\Support\Str::limit((string) $t->tagline, 34) }}</small></span></a>
                                @endforeach
                            </div>
                            <a class="more" href="{{ route('packages.index') }}">View all journeys &rarr;</a>
                        </div>
                        <aside class="mega-promo">
                        <p class="mp-k">Personal planning</p>
                        <h5>Not sure where to start?</h5>
                        <p>Answer a few quick questions and a travel specialist will plan your route.</p>
                        <a class="btn sm" href="{{ route('contact') }}" data-quote>Plan my journey <i aria-hidden="true">&rarr;</i></a>
                    </aside>
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
                                    <a class="txt" href="{{ route('packages.index', ['style' => $slug]) }}"><b>{{ $st['name'] }}</b><em>{{ $st['text'] }}</em><u>Discover <i aria-hidden="true">&rarr;</i></u></a>
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
            <li><a @class(['on' => request()->routeIs('contact')]) href="{{ route('contact') }}">Contact us</a></li>
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
@yield('content')
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
            @if($phone)<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg></span><div><h3>Call Now</h3><a href="tel:{{ $tel }}">{{ $phone }}</a></div></div>@endif
            @if(! empty($site['email']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><div><h3>Email</h3><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></div></div>@endif
            @if(! empty($site['address']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></span><div><h3>Address</h3><p>{!! nl2br(e($site['address'])) !!}</p></div></div>@endif
            @if(! $phone && empty($site['email']) && empty($site['address']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><div><h3>Get in touch</h3><a href="{{ route('contact') }}" data-quote>Send us an enquiry</a></div></div>@endif
        </div>
        <div class="fc"><h3>Destinations</h3>@foreach($destinations as $d)<a href="{{ route('packages.index', ['destination' => $d->slug]) }}">{{ $d->name }}</a>@endforeach<a href="{{ route('packages.index') }}">All packages</a></div>
        <div class="fc"><h3>Travel Styles</h3>@foreach(config('goaustria.travel_styles', []) as $slug => $st)<a href="{{ route('packages.index', ['style' => $slug]) }}">{{ $st['name'] }}</a>@endforeach</div>
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
@unless($noStickyBar ?? false)
<div class="consult">
    <div class="consult-in">
    <p>@if($phone && ! str_contains($phone, '[CONFIRM'))Consult with our Travel Specialists at <a href="tel:{{ $tel }}">{{ $phone }}</a>@else Talk to our Austria travel specialists @endif</p>
    <a class="rq" href="{{ route('contact') }}" data-quote>Request a quote <i aria-hidden="true">&rarr;</i></a>
    </div>
</div>
@endunless
@stack('scripts')
</body>
</html>

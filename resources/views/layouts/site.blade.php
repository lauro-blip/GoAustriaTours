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
    {{-- Lets CSS lay the hero quiz out as one step at a time from the first paint (no layout shift when home.js starts). --}}
    <script>document.documentElement.classList.add('qjs')</script>
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
    @php $design = \App\Support\Design::current(); $designCss = \App\Support\Design::css($design); @endphp
    @stack('preload')
    @if($design['font_body'] === 'inter' || $design['font_heading'] === 'inter')<link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/inter-latin-wght-normal.woff2') }}" crossorigin>@endif
    @if($design['font_heading'] === 'playfair')<link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/playfair-display-latin-wght-normal.woff2') }}" crossorigin>@endif
    @vite(['resources/js/home.js'])
    @unless(request()->routeIs('home'))@vite(['resources/css/inner.css'])@endunless
    {{-- Admin > Design: overrides only what differs from the brand (values are validated hex colours and fixed font lists). --}}
    @if($designCss !== '')<style id="site-design">{!! $designCss !!}</style>@endif
    <script type="application/ld+json">{!! json_encode(\App\Support\Schema::organization($site), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @stack('jsonld')
</head>
<body class="t3" style="margin:0">
@if(\App\Support\Design::isPreview())<div style="position:sticky;top:0;z-index:200;padding:10px 15px;background:#fff4d6;color:#3e251e;font:500 14px/1.4 system-ui,sans-serif;text-align:center;border-bottom:2px solid #dba12c">Design preview (not saved, only you can see it). <a href="{{ route('admin.design.edit') }}" style="color:inherit;font-weight:700;text-decoration:underline">Back to Design</a> to save or discard it.</div>@endif
@if(! empty($site['gtm_id']))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $site['gtm_id'] }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
@endif
<a href="#main" class="skip">Skip to content</a>

{{-- ============ HEADER (items, labels and order from Admin > Menus: App\Support\Menus) ============ --}}
@php
    $mh = $menuHeader;
    $mf = $menuFooter;
    $phoneOk = $phone && ! str_contains($phone, '[CONFIRM');
@endphp
<header class="hdr">
    <div class="wrap">
        <a class="logo" href="{{ route('home') }}" aria-label="{{ $brand }}"><img class="on-dark" src="{{ $img('logo-white.webp') }}" alt="{{ $brand }}" width="500" height="167"><img class="on-light" loading="lazy" src="{{ $img('logo-navy.webp') }}" alt="" width="500" height="167"></a>
        <ul class="nav" aria-label="Main">
            @foreach($mh['items'] as $mi)
                @continue($mi['hidden'])
                @switch($mi['key'])
                    @case('home')
                        <li><a @class(['on' => request()->routeIs('home')]) href="{{ route('home') }}">{{ $mi['label'] }}</a></li>
                        @break
                    @case('destinations')
                        <li><a href="{{ route('destinations.index') }}" @class(['mtrig', 'on' => request()->routeIs('destinations.index') || (request()->routeIs('packages.index') && (request()->filled('destination') || request()->filled('q')))])>{{ $mi['label'] }} <i class="caret"></i></a>
                            @include('layouts.nav.destinations')
                        </li>
                        @break
                    @case('vacations')
                        <li><a href="{{ route('tours.index') }}" @class(['mtrig', 'on' => request()->routeIs('tours.*')])>{{ $mi['label'] }} <i class="caret"></i></a>
                            @include('layouts.nav.vacations')
                        </li>
                        @break
                    @case('styles')
                        <li><a href="{{ route('styles.index') }}" @class(['mtrig', 'on' => request()->routeIs('styles.index') || (request()->routeIs('packages.index') && request()->filled('style'))])>{{ $mi['label'] }} <i class="caret"></i></a>
                            @include('layouts.nav.styles')
                        </li>
                        @break
                    @case('about')
                        <li><button type="button" @class(['on' => request()->routeIs('about', 'journal.*', 'testimonials', 'terms', 'privacy')])>{{ $mi['label'] }} <i class="caret"></i></button>
                            @include('layouts.nav.about')
                        </li>
                        @break
                    @case('contact')
                        <li><a @class(['on' => request()->routeIs('contact')]) href="{{ route('contact') }}">{{ $mi['label'] }}</a></li>
                        @break
                    @default
                        <li><a @class(['on' => \App\Support\Menus::isCurrent($mi['url'])]) href="{{ \App\Support\Menus::href($mi['url']) }}">{{ $mi['label'] }}</a></li>
                @endswitch
            @endforeach
        </ul>
        @if($mh['cta_label'] !== '')
            @if($mh['cta_url'] !== '')<a class="cta" href="{{ \App\Support\Menus::href($mh['cta_url']) }}">{{ $mh['cta_label'] }} <i class="caret"></i></a>@else<a class="cta" href="{{ route('contact') }}" data-quote>{{ $mh['cta_label'] }} <i class="caret"></i></a>@endif
        @endif
        <button class="burger" type="button" data-open-menu aria-label="Open menu"><i></i><i></i><i></i></button>
    </div>
</header>
@include('layouts.nav.mobile')

<main id="main">
@yield('content')
</main>

{{-- ============ FOOTER (texts, columns and links from Admin > Menus; a page can still pass ctaTitle / ctaText) ============ --}}
<section class="fcta">
    <div class="wrap center">
        <p class="fcta-t">{{ $ctaTitle ?? \App\Support\Menus::fill($mf['cta_title'], $brand) }}</p>
        <a class="btn" href="{{ route('contact') }}" data-quote @if(! empty($ctaPackage)) data-package-id="{{ $ctaPackage->id }}" data-package="{{ $ctaPackage->title }}" @endif @if(! empty($ctaInterest)) data-interest="{{ $ctaInterest }}" @endif>{{ $mf['cta_button'] !== '' ? $mf['cta_button'] : 'Get your free quote' }} <i aria-hidden="true">&rarr;</i></a>
        @if(($ctaText ?? $mf['cta_text']) !== '')<p class="fcta-s">{{ $ctaText ?? \App\Support\Menus::fill($mf['cta_text'], $brand) }}</p>@endif
        @if($phone)<a class="fcta-p" href="tel:{{ $tel }}">{{ $phone }}</a>@endif
    </div>
</section>
<footer class="foot">
    <div class="wrap foot-grid">
        @foreach($mf['columns'] as $col)
            @continue($col['hidden'])
            @switch($col['key'])
                @case('contact')
                    <div class="fc fc-contact">
                        @if($col['title'] !== '')<h3>{{ $col['title'] }}</h3>@endif
                        @if($phone)<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg></span><div><h3>Call Now</h3><a href="tel:{{ $tel }}">{{ $phone }}</a></div></div>@endif
                        @if(! empty($site['email']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><div><h3>Email</h3><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></div></div>@endif
                        @if(! empty($site['address']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg></span><div><h3>Address</h3><p>{!! nl2br(e($site['address'])) !!}</p></div></div>@endif
                        @if(! $phone && empty($site['email']) && empty($site['address']))<div class="fci"><span class="fic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span><div><h3>Get in touch</h3><a href="{{ route('contact') }}" data-quote>Send us an enquiry</a></div></div>@endif
                    </div>
                    @break
                @case('destinations')
                    <div class="fc">@if($col['title'] !== '')<h3>{{ $col['title'] }}</h3>@endif<a href="{{ route('destinations.index') }}">All destinations</a>@foreach(collect($navPlaces)->sortByDesc('count')->take(5) as $key => $pl)<a href="{{ route('packages.index', ['place' => $key]) }}">{{ $pl['name'] }}</a>@endforeach<a href="{{ route('packages.index') }}">All packages</a></div>
                    @break
                @case('styles')
                    <div class="fc">@if($col['title'] !== '')<h3>{{ $col['title'] }}</h3>@endif<a href="{{ route('styles.index') }}">All travel styles</a>@foreach(config('goaustria.travel_styles', []) as $slug => $st)<a href="{{ route('packages.index', ['style' => $slug]) }}">{{ $st['name'] }}</a>@endforeach</div>
                    @break
                @case('brand')
                    <div class="fc fc-brand">
                        <img src="{{ $img('logo-white.webp') }}" alt="{{ $brand }}" width="500" height="167" loading="lazy">
                        @if($mf['brand_text'] !== '')<p>{{ \App\Support\Menus::fill($mf['brand_text'], $brand) }}</p>@endif
                    </div>
                    @break
                @default
                    @if(! empty($col['links']))<div class="fc">@if($col['title'] !== '')<h3>{{ $col['title'] }}</h3>@endif @foreach($col['links'] as $l)<a href="{{ \App\Support\Menus::href($l['url']) }}">{{ $l['label'] }}</a>@endforeach</div>@endif
            @endswitch
        @endforeach
    </div>
    <div class="foot-bar">
        <div class="wrap foot-bar-in">
            @if($mf['copyright'] !== '')<p class="copy">{{ \App\Support\Menus::fill($mf['copyright'], $brand) }}</p>@endif
            <nav class="foot-legal" aria-label="Company">@foreach($mf['bottom_links'] as $l)<a href="{{ \App\Support\Menus::href($l['url']) }}">{{ $l['label'] }}</a>@endforeach @foreach($navPages['footer'] ?? [] as $np)<a href="{{ route('pages.show', $np->slug) }}">{{ $np->title }}</a>@endforeach</nav>
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
    <p>@if($phoneOk){{ $mf['consult_text'] }} <a href="tel:{{ $tel }}">{{ $phone }}</a>@else Talk to our Austria travel specialists @endif</p>
    @if($phoneOk)<a class="cph" href="tel:{{ $tel }}" aria-label="Call {{ $phone }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg></a>@endif
    <a class="rq" href="{{ route('contact') }}" data-quote>{{ $mf['consult_button'] !== '' ? $mf['consult_button'] : 'Request a quote' }} <i aria-hidden="true">&rarr;</i></a>
    </div>
</div>
@endunless
@stack('scripts')
</body>
</html>

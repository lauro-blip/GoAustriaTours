@php
    $brand = $site['brand_name'] ?? config('app.name');
    $title = $metaTitle ?? $brand;
    $desc = $metaDescription ?? ($site['default_meta_description'] ?? '');
    $canonical = $canonical ?? url()->current();
    $ogImage = $ogImage ?? (! empty($site['default_social_image']) ? asset('storage/'.$site['default_social_image']) : null);
    $noindex = $noindex ?? false;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $desc }}">
    <link rel="canonical" href="{{ $canonical }}">
    @if($noindex)<meta name="robots" content="noindex, nofollow">@endif

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $brand }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $desc }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $desc }}">
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

    @if(! empty($site['gtm_id']))
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $site['gtm_id'] }}');</script>
    @endif

    {{-- Optional tracking set in Admin > Trust & tracking. Each loads only when its ID is filled in. --}}
    @if(! empty($site['google_ads_id']))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $site['google_ads_id'] }}"></script>
    <script>window.dataLayer = window.dataLayer || []; function gtag(){ dataLayer.push(arguments); } gtag('js', new Date()); gtag('config', @js($site['google_ads_id']));</script>
    @endif
    @if(! empty($site['meta_pixel_id']))
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', @js($site['meta_pixel_id'])); fbq('track', 'PageView');</script>
    @endif
    @if(! empty($site['clarity_id']))
    <script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script",@js($site['clarity_id']));</script>
    @endif
    @stack('tracking')

    {{-- Preload the two above-the-fold font files (latin subset) so text renders without a swap flash. --}}
    <link rel="preload" href="{{ Vite::asset('node_modules/@fontsource/cormorant-garamond/files/cormorant-garamond-latin-500-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('node_modules/@fontsource-variable/dm-sans/files/dm-sans-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    @stack('preload')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json">{!! json_encode(\App\Support\Schema::organization($site), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @stack('jsonld')
</head>
<body class="min-h-screen flex flex-col" x-data>
    @if(! empty($site['gtm_id']))
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $site['gtm_id'] }}" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
    @endif

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-white focus:px-4 focus:py-2 focus:rounded">Skip to content</a>

    @if($transparentHeader ?? false)
        {{-- Home: both bars pinned over the hero; topbar folds away and header turns solid after scrolling --}}
        <div class="fixed inset-x-0 top-0 z-40" x-data="{ scrolled: window.scrollY > 40 }" @scroll.window.passive="scrolled = window.scrollY > 40">
            <div class="grid transition-[grid-template-rows,opacity] duration-500 ease-in-out motion-reduce:transition-none" :class="scrolled ? 'grid-rows-[0fr] opacity-0' : 'grid-rows-[1fr] opacity-100'" :inert="scrolled">
                <div class="overflow-hidden">@include('partials.topbar')</div>
            </div>
            @include('partials.header')
        </div>
    @else
        @include('partials.topbar')
        @include('partials.header')
    @endif

    <main id="main" class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.sticky-cta')
    <x-enquiry-modal />
    @stack('scripts')
</body>
</html>

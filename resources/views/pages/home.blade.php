{{-- Home page: an ordered list of sections from Admin > Pages > Home (App\Support\HomeSections: built-in sections in resources/views/home,
     plus any page builder blocks inserted between them). With nothing saved yet it renders the built-in sections in their original order. --}}
@php
    // All data comes from the database; no invented facts.
    $homeBlocks = \App\Support\HomeSections::normalize($page?->blocks);
    $hero = collect($homeBlocks)->firstWhere('type', 'home_hero');
    $heroImg = \App\Support\Blocks::image(($hero['data'] ?? [])['slide1'] ?? \App\Support\Blocks::blank('home_hero')['data']['slide1']);
    $faqs = collect($homeBlocks)->where('type', 'faq')->reject(fn ($b) => ! empty($b['hidden']))->flatMap(fn ($b) => $b['data']['items'] ?? [])->all();
@endphp
@extends('layouts.site', [
    'metaTitle' => $activeTour ? $activeTour->name.' Tours of Austria | '.($site['brand_name'] ?? 'GoAustriaTours') : ($page?->meta_title ?: ($site['brand_name'] ?? 'GoAustriaTours')),
    'metaDescription' => $page?->meta_description ?: ($site['default_meta_description'] ?? ''),
    'canonical' => route('home'),
])

@push('preload')
@if($heroImg)<link rel="preload" as="image" href="{{ $heroImg['src'] }}" @if($heroImg['srcset']) imagesrcset="{{ $heroImg['srcset'] }}" imagesizes="100vw" @endif fetchpriority="high">@endif
{{-- The H1's italic line uses Playfair italic: preloading it gave a faster first paint in Lighthouse (1.3-1.4 s vs 1.7 s). --}}
@if(\App\Support\Design::usesPlayfair())<link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/playfair-display-latin-wght-italic.woff2') }}" crossorigin>@endif
@endpush

{{-- Sections inserted from the page builder use inner.css; load it on the home page only when there are some (keeps the default home lean). --}}
@if(collect($homeBlocks)->contains(fn ($b) => ! str_starts_with($b['type'], 'home_') && empty($b['hidden'])))
@push('head')
@vite(['resources/css/inner.css'])
@endpush
@endif

@push('jsonld')
@if($faqSchema = \App\Support\Schema::faq($faqs))<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>@endif
@endpush

@section('content')
@php
    // Shared by the home section views (resources/views/home/*).
    $winter = ['hallstatt', 'zellamsee', 'kitzbuhel', 'steinernes-meer', 'tauplitz', 'untersberg', 'salzburg', 'schonbrunn', 'xmas-salzburg'];
    $phone = trim((string) ($site['phone'] ?? ''));
    $tel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $phone));
    $brand = $site['brand_name'] ?? 'GoAustriaTours';
    $tx = fn ($s) => str_replace('{brand}', $brand, (string) $s);
    $navPlaces = config('goaustria.nav_places', []);
    $pkgs = $packages->values()->map(function ($p, $i) use ($winter, $w) {
        $card = \App\Support\Places::card($p);
        $card->image = $w($winter[$i % count($winter)], '-768');

        return $card;
    });
    $featured = $pkgs->take(12)->values();
    $popular = $pkgs->slice(6, 6)->values();
@endphp
@include('blocks._all', ['blocks' => $homeBlocks])
@endsection

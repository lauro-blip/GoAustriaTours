{{-- A page built in Admin > Pages: banner from the page's header fields, then its section blocks (resources/views/blocks). --}}
@php
    $hdr = \App\Support\Blocks::image(['ref' => (string) $page->header_image, 'alt' => '']);
    $bTitle = $page->title;
    $bEyebrow = $page->eyebrow;
    $bLead = $page->intro;
    $bImage = $hdr['src'] ?? $w('hero-wagrain', '-1280');
    $bCrumbs = [[$page->title, null]];
    $blocks = $page->blocks ?? [];
    $faqs = collect($blocks)->where('type', 'faq')->flatMap(fn ($b) => $b['data']['items'] ?? [])->all();
@endphp
@extends('layouts.site', [
    'metaTitle' => $page->meta_title ?: $page->title.' | '.($site['brand_name'] ?? $brand),
    'metaDescription' => $page->meta_description ?: $page->intro,
    'canonical' => route('pages.show', $page->slug),
    'noindex' => $page->noindex || ! $page->is_published,
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], [$page->title, route('pages.show', $page->slug)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@if($faqSchema = \App\Support\Schema::faq($faqs))<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>@endif
@endpush

@section('content')
@include('pages.partials.banner')
@unless($page->is_published)<p class="blk-draft">Draft: only signed-in admins can see this page. Publish it in Admin &gt; Pages.</p>@endunless
@include('blocks._all', ['blocks' => $blocks])
@endsection

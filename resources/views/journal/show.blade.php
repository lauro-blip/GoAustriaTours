@php
    $bTitle = $post->title;
    $bEyebrow = $post->category;
    $bLead = $post->excerpt;
    $bImage = $post->imageUrl();
    $bCrumbs = [['Journal', route('journal.index')], [$post->title, null]];
    $mentioned = \App\Support\Places::inText(strip_tags((string) $post->body).' '.$post->title);
@endphp
@extends('layouts.site', [
    'metaTitle' => $post->meta_title ?: $post->title.' | '.($site['brand_name'] ?? ''),
    'metaDescription' => $post->meta_description ?: $post->excerpt,
    'canonical' => route('journal.show', $post),
    'ogImage' => $post->imageUrl(),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Journal', route('journal.index')], [$post->title, route('journal.show', $post)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $post->title, 'description' => $post->excerpt, 'image' => $post->imageUrl(), 'datePublished' => $post->published_at?->toIso8601String(), 'author' => ['@type' => 'Organization', 'name' => $site['brand_name'] ?? 'GoAustriaTours']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

<article class="sec">
    <div class="wrap narrow">
        <p class="jmeta art-meta"><time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('F j, Y') }}</time><i aria-hidden="true">&middot;</i><span>{{ $post->readingMinutes() }} min read</span></p>
        <div class="prose">{!! $post->body !!}</div>

        @if(count($mentioned))
            <h2 class="pk-h">Places in this article</h2>
            @include('pages.partials.place-cards', ['places' => $mentioned])
        @endif

        <div class="ctabox">
            <h2>Planning your Austria trip?</h2>
            <p>Tell us what you love and a personal travel specialist will design your itinerary, with no obligation.</p>
            <a class="btn" href="{{ route('contact') }}" data-quote>Plan my journey <i aria-hidden="true">&rarr;</i></a>
        </div>
    </div>
</article>

@if($more->isNotEmpty())
<section class="sec tint">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">Keep reading</p><h2 class="h">More from the journal</h2></div>
        <div class="jr">@foreach($more as $p)@include('pages.partials.journal-card', ['post' => $p])@endforeach</div>
    </div>
</section>
@endif
@endsection

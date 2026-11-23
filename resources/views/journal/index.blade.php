@php
    $bTitle = 'Austria Travel Journal';
    $bEyebrow = 'Stories and tips';
    $bLead = 'Ideas, routes and practical advice for planning your trip to Austria.';
    $bImage = asset('images/winter/hero-salzburg-1280.webp');
    $bCrumbs = [['Journal', null]];
@endphp
@extends('layouts.site', [
    'metaTitle' => 'Austria Travel Journal | '.($site['brand_name'] ?? ''),
    'metaDescription' => 'Travel tips, route ideas and destination guides for planning your trip to Austria.',
    'canonical' => route('journal.index'),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Journal', route('journal.index')]]), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')
<section class="sec">
    <div class="wrap">
        @forelse($posts as $post)
            @if($loop->first)<div class="jr">@endif
            @include('pages.partials.journal-card', ['post' => $post])
            @if($loop->last)</div>@endif
        @empty
            <p class="empty">New articles are coming soon.</p>
        @endforelse
        @if($posts->hasPages())
            <nav class="pgn" aria-label="Pagination">
                @if($posts->onFirstPage())<span class="off">&larr; Newer</span>@else<a href="{{ $posts->previousPageUrl() }}">&larr; Newer</a>@endif
                <span>Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
                @if($posts->hasMorePages())<a href="{{ $posts->nextPageUrl() }}">Older &rarr;</a>@else<span class="off">Older &rarr;</span>@endif
            </nav>
        @endif
    </div>
</section>
@endsection

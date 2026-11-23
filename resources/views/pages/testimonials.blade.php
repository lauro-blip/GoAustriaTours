@php
    $bTitle = 'Guest Stories';
    $bEyebrow = 'In their own words';
    $bLead = 'Words from travellers who let us shape their trip to Austria.';
    $bImage = asset('images/winter/steinernes-meer-1280.webp');
    $bCrumbs = [['Guest stories', null]];
@endphp
@extends('layouts.site', [
    'metaTitle' => 'Guest Stories | What Our Travellers Say | '.($site['brand_name'] ?? ''),
    'metaDescription' => 'Read what travellers say about their journeys through Austria.',
    'canonical' => route('testimonials'),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Guest stories', route('testimonials')]]), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
@include('pages.partials.banner')

<section class="sec">
    <div class="wrap">
        @forelse($stories as $t)
            @if($loop->first)<div class="tgrid tcards">@endif
            @php $ini = collect(preg_split('/\s+/', trim($t->name)))->filter()->take(2)->map(fn ($x) => mb_strtoupper(mb_substr($x, 0, 1)))->implode(''); @endphp
            <figure class="rev">
                <span class="qi" aria-hidden="true"><svg viewBox="0 0 48 36" fill="currentColor"><path d="M0 36V19C0 8 6 1.5 17 0l1.6 5C13 6.6 10.5 10 10.5 15H19v21H0zm27 0V19C27 8 33 1.5 44 0l1.6 5C40 6.6 37.5 10 37.5 15H46v21H27z"/></svg></span>
                <blockquote>{{ $t->quote }}</blockquote>
                <figcaption><span class="av" aria-hidden="true">{{ $ini }}</span><span class="who"><b>{{ $t->name }}</b>@if($t->location)<small>{{ $t->location }}</small>@endif</span></figcaption>
            </figure>
            @if($loop->last)</div>@endif
        @empty
            <p class="empty">Guest stories are coming soon.</p>
        @endforelse

        <div class="ctabox">
            <h2>Your Austria, planned around you.</h2>
            <p>Tell us how you like to travel and we will plan the rest.</p>
            <a class="btn" href="{{ route('contact') }}" data-quote>Plan my journey <i aria-hidden="true">&rarr;</i></a>
        </div>
    </div>
</section>
@endsection

@php
    $isAbout = $page->slug === 'about';
    $bTitle = $page->title;
    $bEyebrow = $isAbout ? 'Our story' : ($page->slug === 'terms' || $page->slug === 'privacy' ? 'Legal' : null);
    $bImage = asset('images/winter/'.($isAbout ? 'hero-salzburg' : 'hero-wagrain').'-1280.webp');
    $bCrumbs = [[$page->title, null]];
@endphp
@extends('layouts.site', ['metaTitle' => $page->meta_title, 'metaDescription' => $page->meta_description, 'noindex' => $page->noindex])

@section('content')
@include('pages.partials.banner', ['bClass' => 'slim'])

<section class="sec">
    <div class="wrap narrow">
        <div class="prose">{!! $page->body !!}</div>
    </div>
</section>

@if($isAbout)
<section class="sec tint">
    <div class="wrap">
        <div class="head"><p class="eyebrow dark">How we plan your trip</p><h2 class="h">Three ways to see Austria</h2></div>
        <div class="ways">
            @foreach($tourTypes as $t)
                <a class="way" href="{{ route('tours.show', $t->slug) }}">
                    <img src="{{ $w($wayImg[$t->slug] ?? 'hero-wagrain', '-768') }}" alt="" loading="lazy">
                    <div class="wbody"><h3>{{ $t->name }}</h3><p>{{ \Illuminate\Support\Str::limit($t->tagline ?: $t->summary, 80) }}</p><span class="wgo">Explore {{ $t->name }} <i aria-hidden="true">&rarr;</i></span></div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

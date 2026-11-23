@php
    $bTitle = "We can't find that page";
    $bEyebrow = 'Error 404';
    $bLead = 'It may have moved. Try one of our journeys instead.';
    $bImage = asset('images/winter/hero-montafon-1280.webp');
@endphp
@extends('layouts.site', ['metaTitle' => 'Page not found', 'noindex' => true])

@section('content')
@include('pages.partials.banner', ['bClass' => 'slim'])
<section class="sec">
    <div class="wrap narrow center">
        <p><a class="btn" href="{{ route('home') }}">Home <i aria-hidden="true">&rarr;</i></a> &nbsp; <a class="link" href="{{ route('packages.index') }}">Browse journeys</a></p>
    </div>
</section>
@endsection

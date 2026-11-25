@php
    $bTitle = $page->title;
    $bEyebrow = 'Get in touch';
    $bLead = 'Tell us about the trip you have in mind and a personal travel specialist will reply with a free, no-obligation quote.';
    $bImage = asset('images/winter/hero-montafon-1280.webp');
    $bCrumbs = [['Contact', null]];
    $cPhone = trim((string) ($site['phone'] ?? ''));
    $cTel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $cPhone));
    $cEmail = trim((string) ($site['email'] ?? ''));
    $cAddr = trim((string) ($site['address'] ?? ''));
@endphp
@extends('layouts.site', ['noStickyBar' => true, 'metaTitle' => $page->meta_title, 'metaDescription' => $page->meta_description, 'noindex' => $page->noindex])

@section('content')
@include('pages.partials.banner')

<section class="sec">
    <div class="wrap ct">
        <div class="ct-info">
            <div class="prose">{!! $page->body !!}</div>
            <ul class="ct-list">
                @if($cPhone !== '')<li><span class="ci">PH</span><div><h3>Call us</h3><a href="tel:{{ $cTel }}">{{ $cPhone }}</a></div></li>@endif
                @if($cEmail !== '')<li><span class="ci">EM</span><div><h3>Email</h3><a href="mailto:{{ $cEmail }}">{{ $cEmail }}</a></div></li>@endif
                @if($cAddr !== '')<li><span class="ci">PN</span><div><h3>Address</h3><p>{!! nl2br(e($cAddr)) !!}</p></div></li>@endif
            </ul>
            <div class="ct-steps">
                <h3>What happens next</h3>
                <ol>
                    <li><b>1</b><span>You tell us your dates and how you like to travel.</span></li>
                    <li><b>2</b><span>A travel specialist plans a route around you.</span></li>
                    <li><b>3</b><span>You receive a free quote with no obligation.</span></li>
                </ol>
            </div>
            <p class="ct-note">Prefer to start with a few quick questions? <a href="{{ route('home') }}#hero-quiz">Use the trip planner on the home page</a>.</p>
        </div>
        <div class="pk-card ct-form">
            <h2>Send us your enquiry</h2>
            @include('pages.partials.quote-form', ['formName' => 'contact', 'uid' => 'contact', 'button' => 'Send enquiry'])
        </div>
    </div>
</section>
@endsection

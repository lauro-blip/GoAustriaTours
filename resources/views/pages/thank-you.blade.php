@php
    $bTitle = 'Thank you, we have your enquiry';
    $bEyebrow = 'Enquiry received';
    $bLead = 'A personal travel specialist will be in touch with your free quote soon.';
    $bImage = asset('images/winter/hero-wagrain-1280.webp');
    $tPhone = trim((string) ($site['phone'] ?? ''));
    $tTel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $tPhone));
@endphp
@extends('layouts.site', ['metaTitle' => 'Thank you | '.($site['brand_name'] ?? ''), 'noindex' => true, 'noStickyBar' => true])

@push('tracking')
@if(! empty($site['google_ads_id']) && ! empty($site['google_ads_conversion_label']))
<script>gtag('event', 'conversion', { send_to: @js($site['google_ads_id'].'/'.$site['google_ads_conversion_label']) });</script>
@endif
@if(! empty($site['meta_pixel_id']))
<script>fbq('track', 'Lead');</script>
@endif
@endpush

@push('head')
<script>
    // Conversion event for Google Tag Manager -> Google Ads. See docs/reference/FORMS_AND_TRACKING.md
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ event: 'enquiry_thank_you', form_name: @js(session('enquiry_form')) });
</script>
@endpush

@section('content')
@include('pages.partials.banner', ['bClass' => 'slim'])

<section class="sec">
    <div class="wrap narrow center thanks">
        @if($tPhone !== '')<p class="lede">In a hurry? Call <a href="tel:{{ $tTel }}">{{ $tPhone }}</a>.</p>@endif
        @if(! empty($site['response_promise']))<p class="promise">{{ $site['response_promise'] }}</p>@endif
        <div class="steps">
            <div><b>1</b><h3>We read your request</h3><p>A travel specialist reads what you told us about your dates and the way you like to travel.</p></div>
            <div><b>2</b><h3>We plan your route</h3><p>We shape an itinerary around you, from the route to the hotels and the pace.</p></div>
            <div><b>3</b><h3>You decide</h3><p>Your quote is free and comes with no obligation. Nothing is fixed until you are happy.</p></div>
        </div>
        <p><a class="btn" href="{{ route('home') }}">Back to home <i aria-hidden="true">&rarr;</i></a></p>
    </div>
</section>
@endsection

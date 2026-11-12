@extends('layouts.app', ['metaTitle' => 'Thank you | '.($site['brand_name'] ?? ''), 'noindex' => true])

@push('head')
<script>
    // Conversion event for Google Tag Manager -> Google Ads. See docs/reference/FORMS_AND_TRACKING.md
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ event: 'enquiry_thank_you', form_name: @js(session('enquiry_form')) });
</script>
@endpush

@section('content')
<section class="section">
    <div class="container-page max-w-2xl text-center">
        <x-icon name="check" class="mx-auto size-14 rounded-full bg-brand-soft p-3 text-brand" />
        <h1 class="mt-6 text-5xl">Thank you, we have your enquiry</h1>
        <p class="mt-4 text-lg text-muted">A local Austria expert will be in touch with your free quote soon. In a hurry? Call <a class="underline text-brand" href="tel:{{ $site['phone_tel'] ?? '' }}">{{ $site['phone'] ?? '' }}</a>.</p>
        <div class="mt-8"><x-button :href="route('home')">Back to home</x-button></div>
    </div>
</section>
@endsection

@extends('layouts.app', ['metaTitle' => $page->meta_title, 'metaDescription' => $page->meta_description, 'noindex' => $page->noindex])

@section('content')
<section class="section">
    <div class="container-page grid gap-12 lg:grid-cols-2">
        <div>
            <h1 class="text-5xl">{{ $page->title }}</h1>
            <div class="prose-content mt-6">{!! $page->body !!}</div>
            <ul class="mt-6 space-y-3 text-sm">
                <li><strong>Phone:</strong> <a class="underline" href="tel:{{ $site['phone_tel'] ?? '' }}">{{ $site['phone'] ?? '' }}</a></li>
                <li><strong>Email:</strong> <a class="underline" href="mailto:{{ $site['email'] ?? '' }}">{{ $site['email'] ?? '' }}</a></li>
                <li><strong>Address:</strong> {{ $site['address'] ?? '' }}</li>
            </ul>
        </div>
        <x-card class="p-6 sm:p-8">
            <h2 class="text-3xl">Send us your enquiry</h2>
            <div class="mt-5"><x-enquiry-form form-name="contact" :extended="true" button="Send Enquiry" /></div>
        </x-card>
    </div>
</section>
@endsection

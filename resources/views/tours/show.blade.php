@extends('layouts.app', [
    'metaTitle' => $tourType->meta_title ?: $tourType->name.' tours of Austria | '.($site['brand_name'] ?? ''),
    'metaDescription' => $tourType->meta_description,
    'canonical' => route('tours.show', $tourType),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], [$tourType->name.' Tours', route('tours.show', $tourType)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
<section class="bg-brand text-white">
    <div class="container-page grid gap-10 py-12 lg:grid-cols-[1.1fr_0.9fr] lg:py-16">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-accent">{{ $tourType->tagline }}</p>
            <h1 class="mt-3 text-5xl sm:text-6xl !text-white">{{ $tourType->name }} tours of Austria</h1>
            <p class="mt-5 max-w-xl text-lg text-white/85">{{ $tourType->summary }}</p>
        </div>
        <div class="rounded-2xl border-4 border-quiz bg-white p-6 text-ink">
            <h2 class="font-sans text-lg font-extrabold uppercase tracking-wider text-brand text-center">Get a free {{ $tourType->name }} quote</h2>
            <div class="mt-4"><x-enquiry-form form-name="package-quote" uid="tour" :extended="true" /></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container-page">
        <div class="prose-content max-w-3xl mx-auto">{!! $tourType->body !!}</div>
        @if($packages->isNotEmpty())
            <h2 class="mt-12 mb-8 text-center text-4xl">{{ $tourType->name }} packages</h2>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@foreach($packages as $p)<x-package-card :package="$p" />@endforeach</div>
        @endif
    </div>
</section>
@endsection

@extends('layouts.app', [
    'metaTitle' => $package->seo_title ?: $package->title.' | '.($site['brand_name'] ?? ''),
    'metaDescription' => $package->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $package->summary), 155),
    'canonical' => route('packages.show', $package),
    'noindex' => $package->noindex,
    'ogImage' => $package->social_image ? asset('storage/'.$package->social_image) : null,
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::trip($package), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Packages', route('packages.index')], [$package->title, route('packages.show', $package)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@php $faqSchema = \App\Support\Schema::faq($package->faqs ?: []); @endphp
@if($faqSchema)
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
@endpush

@section('content')
<section class="section">
    <div class="container-page grid gap-10 lg:grid-cols-[1.4fr_1fr]">
        <div>
            <nav aria-label="Breadcrumb" class="text-sm text-muted"><a class="underline" href="{{ route('packages.index') }}">Packages</a> / {{ $package->title }}</nav>
            <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-accent-text">{{ $package->tourType?->name }} &middot; {{ $package->nights }} nights {{ '' }}@if($package->destination) &middot; {{ $package->destination->name }}@endif</p>
            <h1 class="mt-2 text-5xl">{{ $package->title }}</h1>
            <p class="mt-3 text-lg text-muted">{{ $package->summary }}</p>

            @if($package->hero_image)
                <img src="{{ asset('storage/'.$package->hero_image) }}" alt="{{ $package->hero_image_alt }}" width="1200" height="800" class="mt-6 w-full rounded-2xl">
            @else
                <div class="mt-6 aspect-[3/2] rounded-2xl bg-brand-soft flex items-center justify-center text-sm text-muted">[PLACEHOLDER IMAGE]</div>
            @endif

            <div class="prose-content mt-8">{!! $package->description !!}</div>

            @if($package->highlights)
                <h2 class="mt-10 text-3xl">Highlights</h2>
                <ul class="prose-content mt-3">@foreach($package->highlights as $h)<li>{{ $h }}</li>@endforeach</ul>
            @endif

            @if($package->itinerary)
                <h2 class="mt-10 text-3xl">Day by day</h2>
                <div class="mt-3">@foreach($package->itinerary as $day)<x-faq-item :question="$day['title']">{{ $day['body'] }}</x-faq-item>@endforeach</div>
            @endif

            <div class="mt-10 grid gap-8 sm:grid-cols-2">
                @if($package->inclusions)<div><h2 class="text-2xl">Included</h2><ul class="prose-content mt-2">@foreach($package->inclusions as $i)<li>{{ $i }}</li>@endforeach</ul></div>@endif
                @if($package->exclusions)<div><h2 class="text-2xl">Not included</h2><ul class="prose-content mt-2">@foreach($package->exclusions as $i)<li>{{ $i }}</li>@endforeach</ul></div>@endif
            </div>

            @if($package->faqs)
                <h2 class="mt-10 text-3xl">Questions about this tour</h2>
                <div class="mt-3">@foreach($package->faqs as $f)<x-faq-item :question="$f['question']">{{ $f['answer'] }}</x-faq-item>@endforeach</div>
            @endif
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start" aria-label="Get a quote">
            <x-card class="p-6">
                <p class="text-sm text-muted">From</p>
                <p class="text-4xl font-display font-semibold text-brand">{{ $package->price_from ? '$'.number_format($package->price_from).' '.$package->currency : '[CONFIRM: price]' }} <span class="text-sm font-sans font-normal text-muted">per person</span></p>
                <h2 class="mt-4 text-2xl">Get a Quote</h2>
                <div class="mt-4"><x-enquiry-form form-name="package-quote" :package="$package" :extended="true" button="Get a Quote" /></div>
            </x-card>
            <x-card class="mt-6 p-6">
                <h2 class="text-2xl">Ask Us a Question</h2>
                <div class="mt-4"><x-enquiry-form form-name="ask-question" :package="$package" uid="ask" button="Ask Us a Question" /></div>
            </x-card>
        </aside>
    </div>
</section>
@endsection

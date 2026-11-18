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
@php
    $img = $package->hero_image ? asset('storage/'.$package->hero_image) : asset('images/packages/'.$package->slug.'.webp');
    $price = $package->price_from ? '$'.number_format($package->price_from) : null;
    $facts = array_values(array_filter([
        ['calendar', 'Duration', $package->nights.' nights'],
        $package->tourType ? ['guide', 'Style', $package->tourType->name] : null,
        $package->destination ? ['pin', 'Destination', $package->destination->name] : null,
        ['coin', 'From', $price ? $price.' per person' : 'Price on request'],
    ]));
@endphp

{{-- Banner: photo with the title laid over it --}}
<section class="relative isolate overflow-hidden bg-brand-dark text-white">
    <img src="{{ $img }}" alt="{{ $package->hero_image_alt ?: $package->title }}" width="1600" height="700" fetchpriority="high" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover">
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-dark/90 via-brand-dark/50 to-brand-dark/20" aria-hidden="true"></div>
    <div class="container-page flex min-h-[22rem] flex-col justify-end pb-10 pt-24 sm:min-h-[28rem] sm:pb-14">
        <nav aria-label="Breadcrumb" class="text-sm text-white/80"><a class="underline underline-offset-4" href="{{ route('packages.index') }}">Packages</a> <span aria-hidden="true">/</span> {{ $package->title }}</nav>
        <p class="mt-5 text-xs font-semibold uppercase tracking-[0.25em] text-accent">{{ $package->tourType?->name }} &middot; {{ $package->nights }} nights</p>
        <h1 class="mt-3 max-w-3xl text-[32px] sm:text-[44px] lg:text-[56px] !text-white">{{ $package->title }}</h1>
        @if($package->summary)<p class="mt-4 max-w-2xl text-lg text-white/90">{{ $package->summary }}</p>@endif
    </div>
</section>

{{-- Key facts --}}
<section class="border-b border-line bg-soft-white" aria-label="Tour at a glance">
    <div class="container-page">
        <dl class="grid grid-cols-2 divide-x divide-line lg:grid-cols-4">
            @foreach($facts as [$ic, $label, $value])
                <div class="flex items-center gap-3.5 px-4 py-5 first:pl-0 sm:px-6">
                    <x-icon :name="$ic" :stroke="1.2" class="size-7 shrink-0 text-accent-text" />
                    <div><dt class="text-[11px] font-semibold uppercase tracking-[0.18em] text-muted">{{ $label }}</dt><dd class="mt-0.5 font-semibold text-brand">{{ $value }}</dd></div>
                </div>
            @endforeach
        </dl>
    </div>
</section>

<section class="section pb-28 lg:pb-[90px]">
    <div class="container-page grid gap-12 lg:grid-cols-[1.5fr_1fr] lg:gap-14">
        <div class="min-w-0">
            @if(trim(strip_tags((string) $package->description)) !== '' && ! str_contains((string) $package->description, '[CONFIRM'))
                <div class="prose-content text-lg">{!! $package->description !!}</div>
            @endif

            @if($package->highlights)
                <h2 class="mt-12 text-3xl">Highlights</h2>
                <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach($package->highlights as $h)
                        <li class="flex gap-3 rounded-sm border border-accent/25 bg-white p-4 text-sm"><x-icon name="check" :stroke="1.4" class="mt-0.5 size-5 shrink-0 text-accent-text" /><span>{{ $h }}</span></li>
                    @endforeach
                </ul>
            @endif

            @if($package->itinerary)
                <h2 class="mt-14 text-3xl">Day by day</h2>
                <ol class="mt-8 space-y-0">
                    @foreach($package->itinerary as $day)
                        <li class="relative flex gap-5 pb-8 last:pb-0">
                            @unless($loop->last)<span class="absolute left-[1.15rem] top-10 h-[calc(100%-2.5rem)] w-px bg-accent/40" aria-hidden="true"></span>@endunless
                            <span class="relative flex size-9 shrink-0 items-center justify-center rounded-full border border-accent-text/60 bg-ivory text-sm text-accent-text" aria-hidden="true">{{ $loop->iteration }}</span>
                            <div class="min-w-0 pt-1">
                                <h3 class="text-2xl">{{ $day['title'] }}</h3>
                                <p class="mt-2 text-muted">{{ $day['body'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif

            @if($package->inclusions || $package->exclusions)
                <div class="mt-14 grid gap-6 sm:grid-cols-2">
                    @if($package->inclusions)
                        <div class="rounded-sm border border-line bg-white p-6">
                            <h2 class="text-2xl">Included</h2>
                            <ul class="mt-4 space-y-2.5 text-sm">@foreach($package->inclusions as $i)<li class="flex gap-2.5"><x-icon name="check" :stroke="1.4" class="mt-0.5 size-4 shrink-0 text-accent-text" /><span>{{ $i }}</span></li>@endforeach</ul>
                        </div>
                    @endif
                    @if($package->exclusions)
                        <div class="rounded-sm border border-line bg-white p-6">
                            <h2 class="text-2xl">Not included</h2>
                            <ul class="mt-4 space-y-2.5 text-sm">@foreach($package->exclusions as $i)<li class="flex gap-2.5"><x-icon name="cross" :stroke="1.4" class="mt-0.5 size-4 shrink-0 text-muted" /><span>{{ $i }}</span></li>@endforeach</ul>
                        </div>
                    @endif
                </div>
            @endif

            @if($package->faqs)
                <h2 class="mt-14 text-3xl">Questions about this tour</h2>
                <div class="mt-4">@foreach($package->faqs as $f)<x-faq-item :question="$f['question']">{{ $f['answer'] }}</x-faq-item>@endforeach</div>
            @endif
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start" aria-label="Enquire about this tour">
            <x-card class="!rounded-sm !border-accent/25 p-6 sm:p-7">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-muted">{{ $price ? 'Starting from' : 'Price' }}</p>
                <p class="mt-1 font-display text-4xl font-semibold text-brand">{{ $price ?: 'On request' }}@if($price) <span class="font-sans text-sm font-normal text-muted">per person</span>@endif</p>
                <h2 class="mt-6 border-t border-accent/30 pt-6 text-2xl">Request your personal quote</h2>
                <div class="mt-5"><x-enquiry-form form-name="package-quote" :package="$package" :extended="true" button="Request My Quote" /></div>
            </x-card>
            <x-card class="mt-6 !rounded-sm p-6 sm:p-7">
                <h2 class="text-2xl">Have a question first?</h2>
                <div class="mt-4"><x-enquiry-form form-name="ask-question" :package="$package" uid="ask" button="Ask Us a Question" /></div>
            </x-card>
        </aside>
    </div>
</section>

{{-- Mobile: the quote form is far down the page, so keep an enquire bar within reach --}}
<div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-soft-white/95 px-4 py-3 shadow-[0_-8px_24px_-12px_rgb(15_59_89/0.3)] backdrop-blur lg:hidden">
    <div class="mx-auto flex max-w-xl items-center justify-between gap-4">
        <p class="leading-tight"><span class="block text-[10px] uppercase tracking-wider text-muted">{{ $price ? 'Starting from' : 'Price' }}</span><strong class="font-display text-2xl text-brand">{{ $price ?: 'On request' }}</strong></p>
        <x-button @click="$store.modal.show({ package_id: {{ $package->id }} })" aria-label="Enquire about {{ $package->title }}">Enquire</x-button>
    </div>
</div>
@endsection

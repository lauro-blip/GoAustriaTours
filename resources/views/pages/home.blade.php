@extends('layouts.app', [
    'metaTitle' => $page->meta_title,
    'metaDescription' => $page->meta_description,
])

@push('jsonld')
@if($faqSchema = \App\Support\Schema::faq($faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer])))
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
@endpush

@section('content')
{{-- 3. HERO --}}
<section class="relative isolate overflow-hidden bg-brand text-white">
    {{-- PLACEHOLDER background until a licensed hero photo is added (see docs/reference/IMAGE_CREDITS.md) --}}
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,#2f5a4c_0%,#1f3d34_45%,#142a23_100%)]"></div>
    <div class="container-page grid gap-10 py-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:py-20">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-accent">Tailor-made tours of Austria</p>
            <h1 class="mt-3 text-5xl sm:text-6xl lg:text-7xl !text-white">{{ $page->section('hero_title', 'Austria, Planned Beautifully Around You') }}</h1>
            <p class="mt-5 max-w-xl text-lg text-white/85">{{ $page->section('hero_subtitle') }}</p>
            <ul class="mt-8 grid gap-4 sm:grid-cols-3" aria-label="Why book with us">
                @foreach([['shield', 'Best Price Guarantee', '[CONFIRM: wording]'], ['guide', 'Local Experts', 'Austria specialists'], ['star', 'Guest Reviews', '[CONFIRM: rating source]']] as [$ic, $t, $s])
                    <li class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3">
                        <x-icon :name="$ic" class="size-7 text-accent" />
                        <span class="text-sm"><strong class="block text-white">{{ $t }}</strong><span class="text-white/75">{{ $s }}</span></span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div id="quote"><x-hero-quiz :steps="$quizSteps" /></div>
    </div>
</section>

{{-- 4. WHY --}}
<section class="section" aria-labelledby="why-title">
    <div class="container-page">
        <x-section-heading eyebrow="Why GoAustriaTours" :title="$page->section('why_title', 'Why travellers choose us')" />
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['pin', 'Local Austria experts', 'Itineraries built by people who know the country.'],
                ['coin', 'Transparent pricing', '[CONFIRM: pricing promise]'],
                ['shield', 'Peace of mind', '[CONFIRM: support, protection or bonding details]'],
                ['guide', 'Tailored to you', 'Self drive, escorted or coach, shaped around your trip.'],
            ] as [$ic, $t, $d])
                <x-card class="p-6"><x-icon :name="$ic" class="size-9 text-accent-text" /><h3 class="mt-4 text-2xl">{{ $t }}</h3><p class="mt-2 text-sm text-muted">{{ $d }}</p></x-card>
            @endforeach
        </div>
    </div>
</section>

{{-- 5. TOUR TYPES --}}
<section class="section bg-sand" aria-labelledby="types-title">
    <div class="container-page">
        <x-section-heading eyebrow="Ways to travel" title="Choose how you see Austria" subtitle="Three ways to explore. Not sure which suits you? Ask us." />
        <div class="grid gap-6 md:grid-cols-3">
            @foreach($tourTypes as $t)
                <x-card class="flex flex-col">
                    <div class="aspect-[16/9] bg-brand-soft flex items-center justify-center text-xs text-muted">[PLACEHOLDER IMAGE: {{ $t->name }}]</div>
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="text-3xl"><a href="{{ route('tours.show', $t->slug) }}">{{ $t->name }} Tours</a></h3>
                        <p class="mt-1 text-sm font-medium text-accent-text">{{ $t->tagline }}</p>
                        <p class="mt-3 text-sm text-muted">{{ $t->summary }}</p>
                        <div class="mt-auto flex flex-wrap gap-3 pt-6">
                            <x-button @click="$store.modal.show({})" aria-label="Get a quote for {{ $t->name }} tours">Get a Quote</x-button>
                            <x-button variant="outline" :href="route('contact').'?topic='.$t->slug">Ask Us a Question</x-button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
</section>

{{-- 6. FEATURED PACKAGES --}}
@if($packages->isNotEmpty())
<section class="section" aria-labelledby="featured-title">
    <div class="container-page">
        <x-section-heading eyebrow="Featured packages" title="Popular Austria tours" subtitle="Sample packages shown. Prices and itineraries to be confirmed." />
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($packages as $p)<x-package-card :package="$p" />@endforeach
        </div>
        <div class="mt-10 text-center"><x-button variant="secondary" :href="route('packages.index')">View all packages</x-button></div>
    </div>
</section>
@endif

{{-- 7. HOW IT WORKS --}}
<section class="section bg-brand text-white" aria-labelledby="how-title">
    <div class="container-page">
        <x-section-heading eyebrow="How it works" title="Three simple steps" :light="true" />
        <ol class="grid gap-8 md:grid-cols-3">
            @foreach([['Tell us your plans', 'Answer a few quick questions about when, how and who is travelling.'], ['Get a personal quote', 'A local expert prepares a tailored itinerary and price.'], ['Travel with confidence', 'We confirm the details and look after you on the way.']] as $i => [$t, $d])
                <li class="rounded-2xl border border-white/15 p-6">
                    <span class="font-display text-5xl text-accent">{{ $i + 1 }}</span>
                    <h3 class="mt-2 text-2xl !text-white">{{ $t }}</h3>
                    <p class="mt-2 text-white/80">{{ $d }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- 8. TESTIMONIALS --}}
<section class="section" aria-labelledby="reviews-title">
    <div class="container-page">
        <x-section-heading eyebrow="Guest stories" title="What travellers say" subtitle="Placeholder reviews. Replace with real, permitted guest reviews before launch." />
        <div class="grid gap-6 md:grid-cols-3">
            @foreach($testimonials as $t)
                <x-card class="p-6">
                    <blockquote class="text-muted">&ldquo;{{ $t->quote }}&rdquo;</blockquote>
                    <p class="mt-4 text-sm font-semibold text-brand">{{ $t->name }}<span class="font-normal text-muted">, {{ $t->location }}</span></p>
                </x-card>
            @endforeach
        </div>
    </div>
</section>

{{-- 9. FAQ --}}
<section class="section bg-sand" aria-labelledby="faq-title">
    <div class="container-page max-w-3xl">
        <x-section-heading eyebrow="FAQ" title="Questions, answered" />
        <div>@foreach($faqs as $f)<x-faq-item :question="$f->question">{{ $f->answer }}</x-faq-item>@endforeach</div>
    </div>
</section>

{{-- 10. FINAL CTA --}}
<section class="section bg-brand-dark text-center text-white">
    <div class="container-page max-w-3xl">
        <h2 class="text-4xl sm:text-5xl !text-white">{{ $page->section('cta_title', 'Ready to see Austria?') }}</h2>
        <p class="mt-4 text-white/80">{{ $page->section('cta_text') }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <x-button href="#quote">Start the quiz</x-button>
            <x-button variant="light" :href="'tel:'.($site['phone_tel'] ?? '')">Call {{ $site['phone'] ?? '' }}</x-button>
        </div>
    </div>
</section>
@endsection

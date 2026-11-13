@extends('layouts.app', [
    'metaTitle' => $page->meta_title,
    'metaDescription' => $page->meta_description,
    'transparentHeader' => true,
])

@push('jsonld')
@if($faqSchema = \App\Support\Schema::faq($faqs->map(fn ($f) => ['question' => $f->question, 'answer' => $f->answer])))
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
@endpush

@section('content')
{{-- 3. HERO --}}
<section class="relative isolate overflow-hidden bg-brand text-white">
    {{-- Hero background: Austrian landmarks, CSS crossfade (credits in docs/reference/IMAGE_CREDITS.md) --}}
    <div class="absolute inset-0 -z-10 bg-brand-dark" aria-hidden="true">
        @foreach([['hallstatt', 'center'], ['salzburg', 'center'], ['schonbrunn', 'center 70%']] as $i => [$img, $pos])
            <img src="{{ asset('images/hero/'.$img.'.webp') }}" alt="" width="1920" height="{{ ['747', '883', '1055'][$i] }}" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif class="hero-slide absolute inset-0 h-full w-full object-cover" style="object-position: {{ $pos }}">
        @endforeach
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-brand-dark/55 to-brand-dark/80"></div>
    </div>
    <div class="container-page grid gap-10 pb-12 pt-44 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:pb-20 lg:pt-48">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-accent">Tailor-made Austria tours</p>
            <h1 class="mt-3 text-4xl sm:text-5xl lg:text-6xl !text-white">{{ $page->section('hero_title', 'Your Dream Austria, Planned Around You') }}</h1>
            <p class="mt-4 max-w-md text-lg text-white/90">{{ $page->section('hero_subtitle') }}</p>
            <ul class="mt-6 flex max-w-xs flex-col gap-2.5" aria-label="Why book with us">
                @foreach([['shield', 'Best Price Guarantee', '[CONFIRM: wording]'], ['guide', 'Local Experts', 'Austria specialists'], ['star', 'Guest Reviews', '[CONFIRM: rating source]']] as [$ic, $t, $s])
                    <li class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-2.5 backdrop-blur-sm">
                        <x-icon :name="$ic" class="size-7 text-accent" />
                        <span class="text-sm"><strong class="block text-white">{{ $t }}</strong><span class="text-white/75">{{ $s }}</span></span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div id="quote"><x-hero-quiz :steps="$quizSteps" /></div>
    </div>
</section>

{{-- 3b. JOURNEY INTRO (image + numbered points) --}}
<section class="section" aria-labelledby="journey-title">
    <div class="container-page grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
        {{-- PLACEHOLDER photo (cropped from the design mock-up): replace with a licensed/own image, see docs/reference/IMAGE_CREDITS.md --}}
        <img src="{{ asset('images/hero/why-alps.webp') }}" alt="Hiker on an Alpine ridge above the clouds" width="576" height="675" loading="lazy" class="mx-auto w-full max-w-md rounded-sm object-cover lg:max-w-none">
        <div>
            <p class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-ink"><span class="h-px w-8 bg-secondary"></span>Why book with {{ $site['brand_name'] ?? 'GoAustriaTours' }}</p>
            <h2 id="journey-title" class="mt-4 text-4xl sm:text-5xl">Every Austrian Journey,<br><em>Feel Uniquely Yours.</em></h2>
            <p class="mt-5 max-w-xl text-muted">Discover Austria through thoughtfully crafted journeys shaped around your travel style and interests. From majestic Alpine landscapes and picturesque villages to grand imperial cities and authentic local experiences, every detail is designed to make your journey unforgettable.</p>
            <ol class="mt-8 max-w-md divide-y divide-line">
                @foreach([['Fully Bespoke Itineraries', 'Tailored to your interests, pace and travel style.'], ['Handpicked Austrian Stays', 'Boutique properties, Alpine retreats and grand hotels.'], ['Dedicated Travel Expertise & Personal Support', 'Personal support from planning to your return home.']] as $n => [$t, $s])
                    <li class="flex items-start gap-4 py-4 first:pt-0">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full border border-accent-text/60 text-sm text-accent-text" aria-hidden="true">{{ $n + 1 }}</span>
                        <div><h3 class="text-xl font-semibold">{{ $t }}</h3><p class="mt-0.5 text-sm text-muted">{{ $s }}</p></div>
                    </li>
                @endforeach
            </ol>
            <x-button class="mt-8" @click="$store.modal.show({})">Get a Free Quote</x-button>
        </div>
    </div>
</section>

{{-- 4. TOUR TYPES (Ways to travel) --}}
<section class="section bg-sand" aria-labelledby="types-title">
    <div class="container-page">
        <x-section-heading eyebrow="Ways to travel" title="Choose how you see Austria" subtitle="Three ways to explore. Not sure which suits you? Ask us." />
        <div class="grid gap-6 md:grid-cols-3">
            @foreach($tourTypes as $t)
                <x-card class="flex flex-col">
                    <img src="{{ $t->image ? asset('storage/'.$t->image) : asset('images/tours/'.$t->slug.'.webp') }}" alt="{{ $t->image_alt ?: $t->name.' tours of Austria' }}" width="960" height="540" loading="lazy" class="aspect-[16/9] w-full object-cover">
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

{{-- 5. FEATURED PACKAGES (3 per page, ajax pagination: resources/js/featured.js) --}}
@if($packages->isNotEmpty())
<section id="featured" class="section scroll-mt-24" aria-labelledby="featured-title">
    <div class="container-page">
        <x-section-heading eyebrow="Featured packages" title="Popular Austria tours" subtitle="Sample packages shown. Prices and itineraries to be confirmed." />
        <div id="featured-list" data-endpoint="{{ route('packages.featured') }}" aria-live="polite">
            @include('partials.featured-packages')
        </div>
    </div>
</section>
@endif
{{-- 5b. TOP DESTINATIONS (trip cards: text overlaid on the photo, places in 2 columns) --}}
<section class="section bg-sand" aria-labelledby="destinations-title">
    <div class="container-page">
        <x-section-heading eyebrow="Top destinations" title="From Alpine peaks to imperial streets." subtitle="Discover Austria’s breathtaking landscapes, charming villages, and historic cities, each revealing a different side of its timeless beauty." />
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([
                ['The Grand Austrian Journey', 'hero/schonbrunn.webp', ['Vienna', 'Wachau', 'Salzburg', 'Hallstatt', 'Gosau', 'Innsbruck']],
                ['Imperial & Alpine', 'packages/tyrol-private-escorted.webp', ['Vienna', 'Melk', 'Linz', 'Salzburg', 'Zell am See', 'Innsbruck']],
                ['Lakeside Austria Escape', 'packages/alpine-lakes-self-drive.webp', ['Salzburg', 'St. Gilgen', 'St. Wolfgang', 'Hallstatt', 'Gosau', 'Bad Ischl']],
                ['Danube & Vineyards', 'packages/classic-austria-by-coach.webp', ['Vienna', 'Krems', 'Dürnstein', 'Melk', 'Linz', 'Salzburg']],
                ['The Alpine Discovery', 'packages/alpine-highlights-by-coach.webp', ['Innsbruck', 'Seefeld', 'Ötztal', 'Zell am See', 'Kaprun', 'Salzburg']],
                ['Castles & Countryside', 'hero/salzburg.webp', ['Vienna', 'Graz', 'Klagenfurt', 'Villach', 'Innsbruck', 'Salzburg']],
            ] as [$title, $img, $places])
                <article class="group relative isolate flex min-h-[26rem] flex-col justify-end overflow-hidden rounded-[var(--radius-card)] text-white shadow-card">
                    <img src="{{ asset('images/'.$img) }}" alt="" width="800" height="600" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/85 via-black/45 to-black/5" aria-hidden="true"></div>
                    <div class="p-6">
                        <h3 class="text-3xl !text-white">{{ $title }}</h3>
                        <ul class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 text-sm text-white/90" aria-label="Places on {{ $title }}">
                            @foreach($places as $place)
                                <li class="flex items-center gap-2"><span class="size-1.5 shrink-0 rounded-full bg-accent" aria-hidden="true"></span>{{ $place }}</li>
                            @endforeach
                        </ul>
                        <x-button variant="light" :href="route('packages.index')" class="mt-5 !px-5 !py-2.5" aria-label="Explore {{ $title }}">Explore Now</x-button>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-10 text-center"><x-button variant="secondary" :href="route('packages.index')">Explore Trips</x-button></div>
    </div>
</section>

{{-- 6. WHY --}}
<section class="section" aria-labelledby="why-title">
    <div class="container-page">
        <x-section-heading eyebrow="Why GoAustriaTours" :title="$page->section('why_title', 'Why travellers choose us')" />
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([
                ['pin', 'Local Austria experts', ['Itineraries built by people who know the country', 'Routes beyond the obvious stops', 'Insider tips for every region']],
                ['guide', 'Tailored to you', ['Self drive, escorted or coach', 'Your pace, interests and budget', 'Easy changes before you book']],
                ['city', 'Handpicked stays', ['Boutique properties and Alpine retreats', 'Chosen for location and character', 'From city palaces to lakeside inns']],
                ['coin', 'Transparent pricing', ['A clear quote before you commit', 'No pressure, no obligation', '[CONFIRM: pricing promise]']],
                ['shield', 'Peace of mind', ['Support from enquiry to return home', 'A real person to talk to', '[CONFIRM: protection or bonding details]']],
                ['star', 'Considered details', ['Transfers and timings planned', 'Local recommendations on arrival', 'Small touches that make it effortless']],
            ] as [$ic, $t, $points])
                <x-card class="p-8 transition duration-300 hover:-translate-y-1">
                    <span class="flex size-16 items-center justify-center rounded-full border border-accent-text/40 text-accent-text"><x-icon :name="$ic" :stroke="1" class="size-8" /></span>
                    <h3 class="mt-6 text-2xl">{{ $t }}</h3>
                    <ul class="mt-5 space-y-2.5 border-t border-line pt-5 text-sm">
                        @foreach($points as $point)<li class="flex gap-2.5"><x-icon name="check" :stroke="1.2" class="mt-0.5 size-4 shrink-0 text-accent-text" /><span>{{ $point }}</span></li>@endforeach
                    </ul>
                </x-card>
            @endforeach
        </div>
    </div>
</section>

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
        <x-section-heading eyebrow="Guest stories" title="What travellers say" />
        <div class="grid gap-6 md:grid-cols-3 lg:gap-8">
            @foreach($testimonials as $t)
                <x-card class="flex flex-col p-7 sm:p-8">
                    <div class="flex items-center justify-between">
                        <div class="flex gap-0.5 text-[#fbbc04]" role="img" aria-label="5 out of 5 stars">
                            @for($i = 0; $i < 5; $i++)<svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 2.8 2.8 5.9 6.4.9-4.6 4.5 1.1 6.4L12 17.400 6.300 20.500l1.100-6.400L2.800 9.600l6.400-.9z"/></svg>@endfor
                        </div>
                        {{-- Google "G" mark: only keep alongside genuine Google reviews --}}
                        <svg class="size-6" viewBox="0 0 48 48" role="img" aria-label="Google review"><path fill="#EA4335" d="M24 9.500c3.500 0 6.600 1.200 9.100 3.600l6.800-6.800C35.800 2.400 30.300 0 24 0 14.600 0 6.500 5.400 2.600 13.200l7.900 6.100C12.400 13.600 17.700 9.500 24 9.500z"/><path fill="#4285F4" d="M46.500 24.500c0-1.600-.1-3.100-.4-4.500H24v9h12.700c-.6 3-2.300 5.500-4.800 7.200l7.600 5.900c4.400-4.100 7-10.100 7-17.600z"/><path fill="#FBBC05" d="M10.500 28.700c-.5-1.400-.8-3-.8-4.700s.3-3.200.8-4.700l-7.900-6.100C.9 16.500 0 20.100 0 24s.9 7.500 2.600 10.800z"/><path fill="#34A853" d="M24 48c6.500 0 11.900-2.100 15.900-5.800l-7.600-5.900c-2.100 1.400-4.900 2.300-8.300 2.300-6.300 0-11.600-4.100-13.500-9.800l-7.900 6.100C6.500 42.600 14.600 48 24 48z"/></svg>
                    </div>
                    <blockquote class="mt-6 mb-8 flex-1 text-[1.05rem] leading-relaxed text-muted">&ldquo;{{ $t->quote }}&rdquo;</blockquote>
                    <div class="mt-auto flex items-center gap-4 border-t border-line pt-6">
                        {{-- Reviewer avatar: initials circle (swap for a real, permitted photo if the guest provides one) --}}
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand-soft text-sm font-semibold text-brand" aria-hidden="true">{{ \Illuminate\Support\Str::of($t->name)->replaceMatches('/[^\p{L}\s]/u', '')->trim()->explode(' ')->filter(fn ($w) => strcasecmp($w, 'the') !== 0)->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') ?: '?' }}</span>
                        <p class="text-sm"><strong class="block text-brand">{{ $t->name }}</strong><span class="flex items-center gap-1 text-muted"><x-icon name="pin" :stroke="1.4" class="size-3.5" />{{ $t->location }}</span></p>
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
</section>

{{-- 8b. GALLERY (6 destinations + Alpine.js lightbox: Esc closes, arrows/swipe navigate, focus is trapped) --}}
@php
    $gallery = [
        ['vienna', 'Vienna', 'Schönbrunn Palace and its imperial gardens'],
        ['salzburg', 'Salzburg', 'The old town beneath Hohensalzburg Fortress'],
        ['hallstatt', 'Hallstatt', 'A lakeside village in the Salzkammergut'],
        ['innsbruck', 'Innsbruck', 'The Inn river and the Nordkette Alps'],
        ['wachau', 'Wachau & Melk', 'Melk Abbey above the Danube'],
        ['grossglockner', 'Grossglockner', 'Hairpin bends on the High Alpine Road'],
    ];
    $galleryItems = array_map(fn ($g) => ['src' => asset('images/gallery/'.$g[0].'.webp'), 'title' => $g[1], 'caption' => $g[2]], $gallery);
@endphp
<section id="gallery" class="section scroll-mt-24 bg-brand-dark" aria-labelledby="gallery-title"
    x-data="{ open: false, i: 0, items: @js($galleryItems), tx: 0,
        show(n) { this.i = n; this.open = true },
        close() { this.open = false },
        next() { this.i = (this.i + 1) % this.items.length },
        prev() { this.i = (this.i - 1 + this.items.length) % this.items.length } }"
    @keydown.escape.window="close()" @keydown.arrow-right.window="open && next()" @keydown.arrow-left.window="open && prev()">
    <div class="container-page">
        <x-section-heading :light="true" eyebrow="Gallery" title="Austria in pictures" subtitle="Six places travellers ask us about most. Tap a photo to see it larger." />
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3 lg:gap-6">
            @foreach($gallery as $n => [$slug, $title, $caption])
                <button type="button" @click="show({{ $n }})" aria-label="Open photo: {{ $title }}" class="group relative aspect-[4/3] overflow-hidden rounded-[var(--radius-card)] bg-brand-soft shadow-card {{ $n === 0 ? 'col-span-2 lg:col-span-1' : '' }}">
                    <img src="{{ asset('images/gallery/'.$slug.'-thumb.webp') }}" alt="{{ $caption }}" width="800" height="600" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-90 transition group-hover:opacity-100" aria-hidden="true"></span>
                    <span class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-2 p-4 text-left text-white sm:p-5">
                        <span class="font-display text-xl leading-tight sm:text-2xl">{{ $title }}</span>
                        <svg class="size-5 shrink-0 opacity-0 transition group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4h6v6M10 20H4v-6M20 4l-7 7M4 20l7-7"/></svg>
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Lightbox --}}
    <div x-show="open" x-cloak x-trap.noscroll="open" x-transition.opacity.duration.200ms
        class="fixed inset-0 z-[90] flex flex-col items-center justify-center bg-black/90 p-4 sm:p-8" role="dialog" aria-modal="true" aria-label="Photo gallery"
        @click.self="close()" @touchstart.passive="tx = $event.changedTouches[0].clientX"
        @touchend.passive="let d = $event.changedTouches[0].clientX - tx; if (Math.abs(d) > 50) { d < 0 ? next() : prev() }">
        <p class="absolute left-4 top-4 text-sm text-white/70 sm:left-8 sm:top-6" aria-live="polite"><span x-text="i + 1"></span> / <span x-text="items.length"></span></p>
        <button type="button" @click="close()" class="absolute right-3 top-3 rounded-full p-3 text-white/80 hover:bg-white/10 hover:text-white sm:right-6 sm:top-5" aria-label="Close gallery"><x-icon name="close" :stroke="1.5" class="size-7" /></button>

        <button type="button" @click="prev()" class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full p-3 text-white/80 hover:bg-white/10 hover:text-white sm:left-6" aria-label="Previous photo"><x-icon name="chevron" :stroke="1.5" class="size-8 rotate-90" /></button>
        <button type="button" @click="next()" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full p-3 text-white/80 hover:bg-white/10 hover:text-white sm:right-6" aria-label="Next photo"><x-icon name="chevron" :stroke="1.5" class="size-8 -rotate-90" /></button>

        <figure class="flex max-h-full max-w-5xl flex-col items-center" @click.self="close()">
            <img :src="items[i].src" :alt="items[i].caption" class="max-h-[75vh] w-auto max-w-full rounded-md object-contain shadow-2xl">
            <figcaption class="mt-4 text-center text-white">
                <span class="block font-display text-2xl" x-text="items[i].title"></span>
                <span class="block text-sm text-white/70" x-text="items[i].caption"></span>
            </figcaption>
        </figure>
    </div>
</section>

{{-- 8c. SEAMLESS PAYMENTS (centred) --}}
<section class="section relative isolate overflow-hidden" aria-labelledby="payments-title">
    {{-- Alpine background (Grossglockner, see IMAGE_CREDITS.md) under a light overlay so the navy text stays readable --}}
    <div class="absolute inset-0 -z-10" aria-hidden="true">
        <img src="{{ asset('images/gallery/grossglockner.webp') }}" alt="" width="1600" height="1200" loading="lazy" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-ivory/92"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-ivory via-transparent to-ivory"></div>
    </div>
    <div class="container-page text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-text">Travel with confidence</p>
        <h2 id="payments-title" class="mx-auto mt-3 max-w-3xl text-4xl sm:text-5xl">Seamless Payments<br><em class="text-accent-text">Simple, Secure &amp; Stress-Free.</em></h2>
        <p class="mx-auto mt-5 max-w-2xl text-muted">Make booking your Austria getaway simple and stress-free with secure payments, flexible options, and hassle-free confirmations.</p>

        <ul class="mx-auto mt-12 grid max-w-6xl gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['users', 'Individual Payments', 'Each traveller pays their share online with ease.'],
                ['lock', 'Secure Transactions', 'Enjoy safe and reliable payments from anywhere.'],
                ['mail', 'Instant Confirmation', 'Receive booking confirmations straight to your inbox.'],
                ['card', 'Flexible Options', 'Choose convenient payment options that suit your group.'],
            ] as [$ic, $t, $d])
                <li class="flex flex-col items-center">
                    <span class="flex size-16 items-center justify-center rounded-full border border-accent-text/40 text-accent-text"><x-icon :name="$ic" :stroke="1" class="size-8" /></span>
                    <h3 class="mt-5 text-2xl">{{ $t }}</h3>
                    <p class="mt-2 max-w-[16rem] text-sm text-muted">{{ $d }}</p>
                </li>
            @endforeach
        </ul>

        <x-button class="mt-12" @click="$store.modal.show({})">Get a free quote</x-button>
    </div>
</section>

{{-- 8d. DISCOVER AUSTRIA: package carousel (CSS scroll-snap, Alpine prev/next) --}}
@if($carouselPackages->isNotEmpty())
<section class="section bg-sand" aria-labelledby="discover-title"
    x-data="{ atStart: true, atEnd: false,
        update() { const t = $refs.track; this.atStart = t.scrollLeft <= 4; this.atEnd = t.scrollLeft + t.clientWidth >= t.scrollWidth - 4 },
        go(dir) { const t = $refs.track; t.scrollBy({ left: dir * t.clientWidth * 0.85, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }) } }"
    x-init="$nextTick(() => update())" @resize.window.debounce.150ms="update()">
    <div class="container-page">
        <x-section-heading eyebrow="Discover Austria" title="Planning Your Next Austrian Escape?" subtitle="From Austria’s majestic Alpine landscapes to its charming villages and elegant historic cities, discover remarkable destinations, authentic experiences, and unforgettable journeys—thoughtfully tailored around the way you want to explore Austria." />

        <div class="mb-4 flex justify-end gap-2">
            <button type="button" @click="go(-1)" :disabled="atStart" class="inline-flex size-11 items-center justify-center rounded-full border border-line bg-white text-brand transition hover:bg-brand hover:text-white disabled:pointer-events-none disabled:opacity-40" aria-label="Previous packages"><x-icon name="chevron" :stroke="1.5" class="size-5 rotate-90" /></button>
            <button type="button" @click="go(1)" :disabled="atEnd" class="inline-flex size-11 items-center justify-center rounded-full border border-line bg-white text-brand transition hover:bg-brand hover:text-white disabled:pointer-events-none disabled:opacity-40" aria-label="Next packages"><x-icon name="chevron" :stroke="1.5" class="size-5 -rotate-90" /></button>
        </div>

        <div x-ref="track" @scroll.passive.throttle.100ms="update()" tabindex="0" role="region" aria-roledescription="carousel" aria-label="Austria tour packages"
            class="-mx-4 flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth px-4 pb-6 [scrollbar-width:none] sm:-mx-6 sm:px-6 lg:mx-0 lg:px-0 [&::-webkit-scrollbar]:hidden">
            @foreach($carouselPackages as $p)
                <div class="w-[85%] shrink-0 snap-start sm:w-[calc(50%-10px)] lg:w-[calc(33.333%-14px)]" role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $carouselPackages->count() }}">
                    <x-package-card :package="$p" />
                </div>
            @endforeach
        </div>

        <div class="mt-6 text-center"><x-button variant="secondary" :href="route('packages.index')">View all packages</x-button></div>
    </div>
</section>
@endif

{{-- 8e. JOURNAL --}}
@if($posts->isNotEmpty())
<section class="section" aria-labelledby="journal-title">
    <div class="container-page">
        <x-section-heading eyebrow="The Journal" title="Austria travel stories and tips" subtitle="Ideas, routes and practical advice for planning your trip." />
        <div class="grid gap-6 md:grid-cols-3 lg:gap-8">@foreach($posts as $post)<x-journal-card :post="$post" />@endforeach</div>
        <div class="mt-10 text-center"><x-button variant="outline" :href="route('journal.index')">Read the journal</x-button></div>
    </div>
</section>
@endif

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

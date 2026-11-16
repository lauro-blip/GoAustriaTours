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
<section class="relative isolate flex min-h-[100svh] flex-col overflow-hidden bg-brand text-white">
    {{-- Hero background: Austrian landmarks, CSS crossfade (credits in docs/reference/IMAGE_CREDITS.md) --}}
    <div class="absolute inset-0 -z-10 bg-brand-dark" aria-hidden="true">
        @foreach([['hallstatt', 'center'], ['salzburg', 'center'], ['schonbrunn', 'center 70%']] as $i => [$img, $pos])
            <img src="{{ asset('images/hero/'.$img.'.webp') }}" alt="" width="1920" height="{{ ['747', '883', '1055'][$i] }}" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif class="hero-slide absolute inset-0 h-full w-full object-cover" style="object-position: {{ $pos }}">
        @endforeach
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-brand-dark/55 to-brand-dark/80"></div>
    </div>
    <div class="container-page flex flex-1 items-center pb-10 pt-36 lg:pt-40">
        <div>
            <h1 class="!font-semibold normal-case leading-[0.9] tracking-tight !text-white text-[5.5rem] sm:text-[8rem] lg:text-[10rem]">Austria.<span class="sr-only"> Bespoke tours, planned around you</span></h1>
            <p class="mt-5 font-display text-2xl font-semibold italic text-white/90 sm:text-4xl">Refined. Timeless. Extraordinary.</p>
            <ul class="mt-8 flex flex-col gap-2 text-sm text-white/85 sm:flex-row sm:flex-wrap sm:gap-x-7" aria-label="Why book with us">
                @foreach([['calendar', 'Tailor-made, planned around you'], ['guide', 'Local Austria experts'], ['mail', 'Free personal quote']] as [$ic, $t])
                    <li class="flex items-center gap-2.5"><x-icon :name="$ic" :stroke="1.3" class="size-5 text-accent" />{{ $t }}</li>
                @endforeach
            </ul>
            <x-button :href="route('contact')" @click.prevent="$store.modal.show({})" class="mt-9 !px-8 !py-3.5 uppercase tracking-wider">Plan My Journey</x-button>
        </div>
    </div>

    {{-- Bottom bar: destination selector (plain GET form, works without JS) + scrolling place names --}}
    <div class="border-t border-white/15 bg-black/30 backdrop-blur-sm">
        <div class="container-page flex flex-col gap-5 py-5 lg:flex-row lg:items-center lg:gap-10">
            <form action="{{ route('packages.index') }}" method="get" class="shrink-0 border-l-2 border-accent pl-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-accent">The Austrian collection</p>
                <div class="mt-1.5 flex flex-wrap items-center gap-3">
                    <label for="hero-destination" class="text-base font-bold uppercase tracking-wide text-white">Where to next?</label>
                    <select id="hero-destination" name="destination" class="h-10 min-w-40 rounded-sm border border-white/40 bg-transparent px-3 text-sm text-white [&>option]:text-ink">
                        <option value="">All destinations</option>
                        @foreach($destinations as $d)<option value="{{ $d->slug }}">{{ $d->name }}</option>@endforeach
                    </select>
                    <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-sm bg-cta px-4 text-[11px] font-semibold uppercase tracking-wider text-brand transition hover:bg-cta-dark">Explore <span aria-hidden="true">&rarr;</span></button>
                </div>
            </form>
            @php $places = ['Vienna', 'Salzburg', 'Hallstatt', 'Innsbruck', 'Zell am See', 'Wachau', 'Gosau', 'Grossglockner']; @endphp
            <div class="marquee mt-5 min-w-0 flex-1 overflow-hidden" aria-hidden="true">
                <div class="marquee-track flex w-max items-center">
                    @foreach([0, 1] as $copy)
                        @foreach($places as $p)<span class="flex items-center whitespace-nowrap text-sm font-medium text-white/90"><span class="px-6">{{ $p }}</span><span class="text-[10px] text-accent">&#9670;</span></span>@endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3b. JOURNEY INTRO (image + numbered points) --}}
<section class="section" aria-labelledby="journey-title">
    <div class="container-page grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
        {{-- PLACEHOLDER photo (cropped from the design mock-up): replace with a licensed/own image, see docs/reference/IMAGE_CREDITS.md --}}
        <img src="{{ asset('images/hero/why-alps.webp') }}" alt="Hiker on an Alpine ridge above the clouds" width="576" height="675" loading="lazy" class="photo-grade mx-auto w-full max-w-md rounded-sm object-cover lg:max-w-none">
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
            <x-button class="mt-8" @click="$store.modal.show({})">Plan My Journey</x-button>
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
                    <img src="{{ $t->image ? asset('storage/'.$t->image) : asset('images/tours/'.$t->slug.'.webp') }}" alt="{{ $t->image_alt ?: $t->name.' tours of Austria' }}" width="960" height="540" loading="lazy" class="photo-grade aspect-[16/9] w-full object-cover">
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="text-3xl"><a href="{{ route('tours.show', $t->slug) }}">{{ $t->name }} Tours</a></h3>
                        <p class="mt-1 text-sm font-medium text-accent-text">{{ $t->tagline }}</p>
                        <p class="mt-3 text-sm text-muted">{{ $t->summary }}</p>
                        <div class="mt-auto flex flex-wrap gap-3 pt-6">
                            <x-button @click="$store.modal.show({})" aria-label="Enquire about {{ $t->name }} tours">Enquire Now</x-button>
                            <x-button variant="outline" :href="route('contact').'?topic='.$t->slug">Speak With Us</x-button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
</section>

{{-- 5. FEATURED PACKAGES (3 per page, ajax pagination: resources/js/featured.js) --}}
@if($packages->isNotEmpty())
<section id="featured" class="section scroll-mt-24 bg-soft-white" aria-labelledby="featured-title">
    <div class="container-page">
        <div class="mb-10 flex flex-col gap-6 lg:mb-12 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="flex items-center gap-3 text-xs font-medium uppercase tracking-[0.2em] text-muted"><span class="h-px w-8 bg-secondary"></span>Featured destinations</p>
                <h2 id="featured-title" class="mt-4 text-4xl sm:text-5xl lg:text-6xl !font-semibold">Your Journey, <em>Beautifully Considered.</em></h2>
                <p class="mt-5 max-w-xl text-brand">Discover Austria’s breathtaking landscapes, charming villages, and historic cities, each revealing a different side of its timeless beauty.</p>
            </div>
            <a href="{{ route('packages.index') }}" class="inline-flex h-11 shrink-0 items-center justify-center self-start bg-cta px-9 text-xs font-medium uppercase tracking-[0.12em] text-brand transition hover:bg-cta-dark lg:self-auto">Explore Trips</a>
        </div>
        <div id="featured-list" data-endpoint="{{ route('packages.featured') }}" aria-live="polite">
            @include('partials.featured-packages')
        </div>
    </div>
</section>
@endif
{{-- 5b. TOP DESTINATIONS (trip cards: text overlaid on the photo, places in 2 columns) --}}
<section class="section" aria-labelledby="destinations-title">
    <div class="container-page">
        <div class="mb-10 flex flex-col gap-6 lg:mb-12 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="flex items-center gap-3 text-xs font-medium uppercase tracking-[0.2em] text-muted"><span class="h-px w-8 bg-secondary"></span>Top destinations</p>
                <h2 id="destinations-title" class="mt-4 text-4xl sm:text-5xl lg:text-6xl !font-semibold">From Alpine Peaks <em>To Imperial Streets.</em></h2>
                <p class="mt-5 max-w-xl text-brand">Discover Austria’s breathtaking landscapes, charming villages, and historic cities, each revealing a different side of its timeless beauty.</p>
            </div>
            <a href="{{ route('packages.index') }}" class="inline-flex h-11 shrink-0 items-center justify-center self-start bg-cta px-9 text-xs font-medium uppercase tracking-[0.12em] text-brand transition hover:bg-cta-dark lg:self-auto">Explore Trips</a>
        </div>
        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([
                ['The Grand Austrian Journey', 'hero/schonbrunn.webp', ['Vienna', 'Wachau', 'Salzburg', 'Hallstatt', 'Gosau', 'Innsbruck']],
                ['Imperial & Alpine', 'packages/tyrol-private-escorted.webp', ['Vienna', 'Melk', 'Linz', 'Salzburg', 'Zell am See', 'Innsbruck']],
                ['Lakeside Austria Escape', 'packages/alpine-lakes-self-drive.webp', ['Salzburg', 'St. Gilgen', 'St. Wolfgang', 'Hallstatt', 'Gosau', 'Bad Ischl']],
                ['Danube & Vineyards', 'packages/classic-austria-by-coach.webp', ['Vienna', 'Krems', 'Dürnstein', 'Melk', 'Linz', 'Salzburg']],
                ['The Alpine Discovery', 'packages/alpine-highlights-by-coach.webp', ['Innsbruck', 'Seefeld', 'Ötztal', 'Zell am See', 'Kaprun', 'Salzburg']],
                ['Castles & Countryside', 'hero/salzburg.webp', ['Vienna', 'Graz', 'Klagenfurt', 'Villach', 'Innsbruck', 'Salzburg']],
            ] as [$title, $img, $places])
                <article class="group relative isolate flex min-h-[20rem] flex-col justify-end overflow-hidden text-white sm:min-h-[22rem]">
                    <img src="{{ asset('images/'.$img) }}" alt="" width="800" height="600" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-dark/90 via-brand-dark/50 to-brand-dark/15" aria-hidden="true"></div>
                    <div class="flex flex-1 flex-col justify-end px-6 pb-6 pt-24">
                        <h3 class="text-[1.65rem] !font-bold !text-white">{{ $title }}</h3>
                        <ul class="mt-3.5 grid grid-cols-3 gap-x-3 gap-y-2.5 text-[13px] text-white/95" aria-label="Places on {{ $title }}">
                            @foreach($places as $place)
                                <li class="flex min-w-0 items-center gap-1.5"><x-icon name="pin" :stroke="1.4" class="size-4 shrink-0 text-accent" /><span class="truncate">{{ $place }}</span></li>
                            @endforeach
                        </ul>
                        <a href="{{ route('packages.index') }}" class="mt-6 flex items-center gap-1.5 self-end border-b border-accent pb-1.5 text-[11px] font-semibold uppercase tracking-[0.14em] text-white" aria-label="Explore {{ $title }}">Explore now <svg class="size-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M3 9l6-6M4 3h5v5"/></svg></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- 6. WHY --}}
<section class="section bg-sand" aria-labelledby="why-title">
    <div class="container-page">
        <x-section-heading eyebrow="Why GoAustriaTours" :title="$page->section('why_title', 'Why travellers choose us')" />
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([
                ['pin', 'Local Austria experts', ['Itineraries built by people who know the country', 'Routes beyond the obvious stops', 'Insider tips for every region']],
                ['guide', 'Tailored to you', ['Self drive, escorted or coach', 'Your pace, interests and budget', 'Easy changes before you book']],
                ['city', 'Handpicked stays', ['Boutique properties and Alpine retreats', 'Chosen for location and character', 'From city palaces to lakeside inns']],
                ['coin', 'Transparent pricing', ['A clear quote before you commit', 'No pressure, no obligation']],
                ['shield', 'Peace of mind', ['Support from enquiry to return home', 'A real person to talk to']],
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

{{-- 7b. MEET YOUR PLANNER: shows only when a name and bio are set in Admin > Settings > Planner (never invented) --}}
@if(! empty($site['planner_name']) && ! empty($site['planner_bio']))
<section class="section bg-sand" aria-labelledby="planner-title">
    <div class="container-page grid items-center gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16">
        <div class="mx-auto w-full max-w-sm">
            @if(! empty($site['planner_photo']))
                <img src="{{ asset('storage/'.$site['planner_photo']) }}" alt="{{ $site['planner_name'] }}" width="640" height="800" loading="lazy" class="aspect-[4/5] w-full rounded-sm object-cover shadow-card">
            @else
                <div class="flex aspect-[4/5] w-full items-center justify-center rounded-sm bg-brand font-display text-7xl text-accent shadow-card" aria-hidden="true">{{ \Illuminate\Support\Str::of($site['planner_name'])->explode(' ')->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}</div>
            @endif
        </div>
        <div>
            <p class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-ink"><span class="h-px w-8 bg-secondary"></span>Meet your planner</p>
            <h2 id="planner-title" class="mt-4 text-4xl sm:text-5xl">{{ $site['planner_name'] }}</h2>
            @if(! empty($site['planner_role']))<p class="mt-2 text-sm font-medium uppercase tracking-wider text-accent-text">{{ $site['planner_role'] }}</p>@endif
            <div class="mt-6 max-w-xl space-y-4 text-muted">
                @foreach(preg_split('/\R{2,}/', trim($site['planner_bio'])) as $para)<p>{{ $para }}</p>@endforeach
            </div>
            <x-button class="mt-8" @click="$store.modal.show({})">Plan My Journey</x-button>
        </div>
    </div>
</section>
@endif

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
    // [file slug, title, photo description (alt + lightbox caption), short tagline shown on the tile]
    $gallery = [
        ['hallstatt', 'Hallstatt', 'A lakeside village in the Salzkammergut', 'Lakeside serenity'],
        ['innsbruck', 'Innsbruck', 'The Inn river and the Nordkette Alps', 'Alpine beauty'],
        ['salzburg', 'Salzburg', 'The old town beneath Hohensalzburg Fortress', 'Baroque charm'],
        ['wachau', 'Wachau', 'Melk Abbey above the Danube', 'Vineyard escapes'],
        ['vienna', 'Vienna', 'Schönbrunn Palace and its imperial gardens', 'Imperial elegance'],
        ['grossglockner', 'Grossglockner', 'Hairpin bends on the High Alpine Road', 'Mountain drives'],
    ];
    $galleryItems = array_map(fn ($g) => ['src' => asset('images/gallery/'.$g[0].'.webp'), 'title' => $g[1], 'caption' => $g[2]], $gallery);
@endphp
<section id="gallery" class="section scroll-mt-24 bg-brand-soft" aria-labelledby="gallery-title"
    x-data="{ open: false, i: 0, items: @js($galleryItems), tx: 0,
        show(n) { this.i = n; this.open = true },
        close() { this.open = false },
        next() { this.i = (this.i + 1) % this.items.length },
        prev() { this.i = (this.i - 1 + this.items.length) % this.items.length } }"
    @keydown.escape.window="close()" @keydown.arrow-right.window="open && next()" @keydown.arrow-left.window="open && prev()">
    <div class="container-page">
        <div class="mx-auto max-w-3xl text-center">
            <p class="flex items-center justify-center gap-3 text-xs font-medium uppercase tracking-[0.2em] text-muted"><span class="h-px w-8 bg-secondary"></span>The gallery</p>
            <h2 id="gallery-title" class="mt-4 text-4xl sm:text-5xl lg:text-6xl !font-semibold">Austria, <em>Frame By Frame.</em></h2>
            <p class="mx-auto mt-5 max-w-2xl text-sm text-brand sm:text-base">From majestic Alpine landscapes to charming historic towns, discover Austria’s most captivating destinations, unforgettable experiences, and hidden gems. Tap a photo to see it larger.</p>
        </div>
        {{-- Mosaic: one tall photo, four standard tiles and a wide panorama strip (stacks in 2 columns on mobile) --}}
        <div class="mx-auto mt-12 grid max-w-[70rem] grid-cols-2 gap-2 lg:auto-rows-[12.5rem] lg:grid-cols-3">
            @foreach($gallery as $n => [$slug, $title, $caption, $tagline])
                @php $span = match ($n) { 0 => 'col-span-2 aspect-[4/3] lg:col-span-1 lg:row-span-2 lg:aspect-auto', 5 => 'col-span-2 aspect-[16/9] lg:col-span-3 lg:aspect-auto', default => 'aspect-[4/3] lg:aspect-auto' }; @endphp
                <button type="button" @click="show({{ $n }})" aria-label="Open photo: {{ $title }}" class="group relative isolate overflow-hidden bg-brand-dark text-left {{ $span }}">
                    <img src="{{ asset('images/gallery/'.$slug.($n === 0 || $n === 5 ? '' : '-thumb').'.webp') }}" alt="{{ $caption }}" width="800" height="600" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    <span class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-dark/85 via-brand-dark/20 to-transparent" aria-hidden="true"></span>
                    <span class="absolute inset-x-0 bottom-0 block p-4 text-white sm:p-6">
                        <span class="flex items-center gap-2.5 text-[10px] font-semibold uppercase tracking-[0.18em] text-accent sm:text-[11px]"><span class="h-px w-6 bg-accent" aria-hidden="true"></span>{{ $tagline }}</span>
                        <span class="mt-1.5 block font-display text-2xl font-bold leading-tight sm:text-[1.7rem]">{{ $title }}</span>
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
            <x-button :href="route('contact')" @click.prevent="$store.modal.show({})">Plan My Journey</x-button>
            @if(! empty($site['phone']))
                <x-button variant="light" :href="'tel:'.preg_replace('/[^\d+]/', '', ($site['phone_tel'] ?? '') ?: $site['phone'])">Call {{ $site['phone'] }}</x-button>
            @endif
        </div>
    </div>
</section>
@endsection

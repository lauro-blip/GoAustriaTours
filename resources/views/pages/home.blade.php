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
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-brand-dark/50 to-brand-dark/85"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-brand-dark/60 via-transparent to-transparent"></div>
    </div>
    <div class="container-page md:!max-w-[1510px] grid flex-1 items-center gap-10 pb-16 pt-36 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)] lg:gap-12 lg:pt-40">
        <div>
            <p class="flex items-center gap-4 text-[14px] font-semibold uppercase tracking-[0.35em] text-accent"><span class="h-px w-12 bg-accent" aria-hidden="true"></span>Bespoke journeys</p>
            <h1 class="mt-6 normal-case leading-[0.9] tracking-tight !text-white text-[5.5rem] sm:text-[8rem] xl:text-[9.5rem]">Austria.<span class="sr-only"> Bespoke tours, planned around you</span></h1>
            <p class="mt-6 max-w-xl font-display text-2xl italic leading-snug text-white/90 sm:text-4xl">Refined. Timeless. <span class="text-accent">Extraordinary.</span></p>
            <ul class="mt-10 flex flex-wrap items-center gap-y-2 border-t border-white/20 pt-6 text-[14px] font-medium uppercase tracking-[0.2em] text-white/90" aria-label="Why book with us">
                @foreach(['Tailor-made', 'Local Austria experts', 'Personal quote'] as $t)
                    <li class="flex items-center">{{ $t }}@unless($loop->last)<span class="px-4 text-[7px] text-accent" aria-hidden="true">&#9670;</span>@endunless</li>
                @endforeach
            </ul>
        </div>
        <x-hero-quiz :steps="$quizSteps" />
    </div>

</section>

{{-- 3a. TRUST STRIP: built only from Admin > Trust & tracking. Blank or "[CONFIRM" values are not shown, and the whole strip disappears when nothing is filled in. --}}
@php
    $marks = collect(range(1, 4))->map(fn ($n) => [
        'label' => trim((string) ($site["trust_mark_{$n}_label"] ?? '')),
        'logo' => trim((string) ($site["trust_mark_{$n}_logo"] ?? '')),
        'url' => trim((string) ($site["trust_mark_{$n}_url"] ?? '')),
    ])->filter(fn ($m) => $m['label'] !== '' || $m['logo'] !== '')->values();
    $established = trim((string) ($site['established_line'] ?? ''));
    $reviewText = trim((string) ($site['review_summary'] ?? ''));
    $reviewUrl = trim((string) ($site['review_url'] ?? ''));
@endphp
@if($marks->isNotEmpty() || $established !== '' || $reviewText !== '')
<section class="border-b border-line bg-soft-white" aria-label="Why you can trust us">
    <div class="container-page flex flex-col items-center gap-x-12 gap-y-5 py-7 text-center lg:flex-row lg:justify-between lg:text-left">
        @if($established !== '')
            <p class="font-display text-2xl italic leading-snug text-brand lg:whitespace-nowrap">{{ $established }}</p>
        @endif
        @if($marks->isNotEmpty())
            <ul class="flex flex-wrap items-center justify-center gap-x-8 gap-y-4 lg:flex-nowrap lg:whitespace-nowrap" aria-label="Memberships and protection">
                @foreach($marks as $m)
                    <li>
                        @if($m['url'] !== '')<a href="{{ $m['url'] }}" target="_blank" rel="noopener" class="block">@endif
                        @if($m['logo'] !== '')
                            <img src="{{ asset('storage/'.$m['logo']) }}" alt="{{ $m['label'] ?: 'Membership logo' }}" class="h-11 w-auto max-w-[9rem] object-contain" loading="lazy">
                        @else
                            <span class="inline-flex items-center gap-2.5 text-sm font-semibold uppercase tracking-[0.18em] text-brand"><span class="text-[9px] text-accent" aria-hidden="true">&#9670;</span>{{ $m['label'] }}</span>
                        @endif
                        @if($m['url'] !== '')</a>@endif
                    </li>
                @endforeach
            </ul>
        @endif
        @if($reviewText !== '')
            <p class="text-base font-medium text-brand lg:whitespace-nowrap">
                @if($reviewUrl !== '')<a href="{{ $reviewUrl }}" target="_blank" rel="noopener" class="underline decoration-accent decoration-2 underline-offset-4 hover:text-accent-text">{{ $reviewText }}</a>@else{{ $reviewText }}@endif
            </p>
        @endif
    </div>
</section>
@endif

{{-- 3b. JOURNEY INTRO (image + numbered points) --}}
<section class="section bg-soft-white" aria-labelledby="journey-title">
    <div class="container-page grid items-center gap-14 lg:grid-cols-[5fr_6fr] lg:gap-24">
        {{-- PLACEHOLDER photo (cropped from the design mock-up): replace with a licensed/own image, see docs/reference/IMAGE_CREDITS.md --}}
        <div class="relative mx-auto w-full max-w-md pb-6 pr-6 lg:max-w-none">
            <span class="absolute bottom-0 right-0 top-6 left-6 border border-accent/60" aria-hidden="true"></span>
            <img src="{{ asset('images/hero/why-alps.webp') }}" alt="Hiker on an Alpine ridge above the clouds" width="576" height="675" loading="lazy" class="photo-grade relative aspect-[4/5] w-full object-cover shadow-[0_30px_60px_-30px_rgb(10_42_64/0.6)]">
            <p class="absolute -left-3 bottom-12 bg-brand px-5 py-3 font-display text-lg font-semibold italic text-white shadow-xl sm:-left-6">Austria, <span class="text-accent">your way.</span></p>
        </div>
        <div>
            <p class="flex items-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-10 bg-accent" aria-hidden="true"></span>Why book with {{ $site['brand_name'] ?? 'GoAustriaTours' }}</p>
            <h2 id="journey-title" class="mt-5 text-[32px] leading-[1.05] sm:text-[44px] lg:text-[56px]">Every Austrian Journey,<br><em class="text-accent-text">Feel Uniquely Yours.</em></h2>
            <p class="mt-6 max-w-xl text-lg leading-relaxed text-muted">Discover Austria through thoughtfully crafted journeys shaped around your travel style and interests. From majestic Alpine landscapes and picturesque villages to grand imperial cities and authentic local experiences, every detail is designed to make your journey unforgettable.</p>
            <ol class="mt-10 max-w-xl border-t border-accent/40">
                @foreach([['Fully Bespoke Itineraries', 'Tailored to your interests, pace and travel style.'], ['Handpicked Austrian Stays', 'Boutique properties, Alpine retreats and grand hotels.'], ['Dedicated Travel Expertise & Personal Support', 'Personal support from planning to your return home.']] as $n => [$t, $s])
                    <li class="flex items-start gap-6 border-b border-line py-6">
                        <span class="font-display text-5xl font-semibold leading-none text-accent" aria-hidden="true">0{{ $n + 1 }}</span>
                        <div><h3 class="text-2xl">{{ $t }}</h3><p class="mt-1.5 text-muted">{{ $s }}</p></div>
                    </li>
                @endforeach
            </ol>
            <x-button class="mt-10 !px-8 !py-3.5 uppercase tracking-wider" @click="$store.modal.show({})">Plan My Journey</x-button>
        </div>
    </div>
</section>

{{-- 3c. FEATURED PACKAGES: every is_featured package in a swipeable carousel (CSS scroll-snap + Alpine arrows), right after "Why book with us" --}}
@if($featuredPackages->isNotEmpty())
<section id="featured" class="section relative isolate scroll-mt-24 overflow-hidden bg-ivory" aria-labelledby="featured-title"
    x-data="{ atStart: true, atEnd: false, progress: 0,
        update() { const t = $refs.track; const max = t.scrollWidth - t.clientWidth; this.atStart = t.scrollLeft <= 4; this.atEnd = t.scrollLeft + t.clientWidth >= t.scrollWidth - 4; this.progress = max > 0 ? Math.min(1, Math.max(0, t.scrollLeft / max)) : 1 },
        go(dir) { const t = $refs.track; t.scrollBy({ left: dir * t.clientWidth * 0.85, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' }) } }"
    x-init="$nextTick(() => update())" @resize.window.debounce.150ms="update()">
    <div class="pointer-events-none absolute -right-40 top-0 -z-10 size-[34rem] bg-[radial-gradient(closest-side,rgb(219_161_44/0.1),transparent)]" aria-hidden="true"></div>
    <div class="container-page">
        <div class="mb-12 flex flex-col gap-8 lg:mb-16 lg:flex-row lg:items-end lg:justify-between lg:gap-16">
            <div class="max-w-3xl">
                <p class="flex items-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-10 bg-accent" aria-hidden="true"></span>Featured packages</p>
                <h2 id="featured-title" class="mt-5 text-[32px] leading-[1.05] sm:text-[44px] lg:text-[56px]">Our Featured <em class="text-accent-text">Itineraries.</em></h2>
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-brand/85">A handful of itineraries to start from. Each one can be adapted to your dates, pace and interests.</p>
            </div>
            <div class="flex shrink-0 gap-3">
                <button type="button" @click="go(-1)" :disabled="atStart" class="inline-flex size-14 items-center justify-center rounded-full border border-accent/70 bg-transparent text-brand transition duration-300 hover:bg-brand hover:text-white disabled:pointer-events-none disabled:opacity-30" aria-label="Previous packages"><x-icon name="chevron" :stroke="1.3" class="size-5 rotate-90" /></button>
                <button type="button" @click="go(1)" :disabled="atEnd" class="inline-flex size-14 items-center justify-center rounded-full border border-accent/70 bg-transparent text-brand transition duration-300 hover:bg-brand hover:text-white disabled:pointer-events-none disabled:opacity-30" aria-label="Next packages"><x-icon name="chevron" :stroke="1.3" class="size-5 -rotate-90" /></button>
            </div>
        </div>

        <div x-ref="track" @scroll.passive.throttle.100ms="update()" tabindex="0" role="region" aria-roledescription="carousel" aria-label="Featured Austria tour packages"
            class="-mx-[15px] flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth px-[15px] pb-8 pt-1 [scrollbar-width:none] md:-mx-[60px] md:px-[60px] lg:mx-0 lg:px-0 [&::-webkit-scrollbar]:hidden">
            @foreach($featuredPackages as $p)
                <div class="w-[85%] shrink-0 snap-start sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]" role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $featuredPackages->count() }}">
                    <x-package-card :package="$p" />
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex items-center gap-8">
            <div class="relative h-px flex-1 bg-line" aria-hidden="true"><span class="absolute inset-y-[-1px] left-0 h-[3px] w-1/4 bg-accent transition-[left] duration-200" :style="`left: ${progress * 75}%`"></span></div>
            <a href="{{ route('packages.index') }}" class="shrink-0 border-b-2 border-accent pb-1 text-xs font-semibold uppercase tracking-[0.25em] text-brand transition hover:text-accent-text">View all packages <span aria-hidden="true">&rarr;</span></a>
        </div>
    </div>
</section>
@endif

{{-- 4. TOUR TYPES (Ways to travel): one compact row of three cards --}}
<section class="section bg-soft-white" aria-labelledby="types-title">
    <div class="container-page">
        <div class="mx-auto mb-10 max-w-3xl text-center lg:mb-12">
            <p class="flex items-center justify-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-10 bg-accent" aria-hidden="true"></span>Ways to travel<span class="h-px w-10 bg-accent" aria-hidden="true"></span></p>
            <h2 id="types-title" class="mt-4 text-[32px] leading-[1.05] sm:text-[44px] lg:text-[56px]">Choose How You <em class="text-accent-text">See Austria.</em></h2>
            <p class="mt-4 text-lg lg:text-xl text-brand/85">Three ways to explore. Not sure which suits you? Ask us.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-3 lg:gap-8">
            @foreach($tourTypes as $t)
                <article class="group relative flex flex-col bg-white shadow-[0_20px_50px_-30px_rgb(10_42_64/0.45)] transition duration-500 hover:-translate-y-1 hover:shadow-[0_30px_60px_-28px_rgb(10_42_64/0.55)] motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                    <div class="relative aspect-[16/11] overflow-hidden bg-brand-dark">
                        <img src="{{ $t->image ? asset('storage/'.$t->image) : asset('images/tours/'.$t->slug.'.webp') }}" alt="{{ $t->image_alt ?: $t->name.' tours of Austria' }}" width="960" height="660" loading="lazy" class="photo-grade h-full w-full object-cover transition duration-[1200ms] ease-out group-hover:scale-105 motion-reduce:transition-none">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/85 via-brand-dark/10 to-transparent" aria-hidden="true"></div>
                        <span class="pointer-events-none absolute inset-3 border border-accent/0 transition duration-500 group-hover:border-accent/70" aria-hidden="true"></span>
                        <span class="absolute left-5 top-4 font-display text-lg italic text-white/90" aria-hidden="true">{{ ['I', 'II', 'III', 'IV', 'V'][$loop->index] ?? $loop->iteration }}</span>
                        <h3 class="absolute inset-x-0 bottom-0 p-5 text-3xl !text-white"><a href="{{ route('tours.show', $t->slug) }}" class="after:absolute after:inset-0">{{ $t->name }} <em class="text-accent">Tours</em></a></h3>
                    </div>
                    <div class="flex flex-1 flex-col border-t-2 border-accent p-5 sm:p-6">
                        @if($t->tagline)<p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-text">{{ $t->tagline }}</p>@endif
                        <p class="mt-3 text-base leading-relaxed text-brand/85">{{ $t->summary }}</p>
                        <div class="relative z-10 mt-auto flex items-center justify-between gap-4 pt-5">
                            <x-button @click="$store.modal.show({})" aria-label="Enquire about {{ $t->name }} tours">Enquire</x-button>
                            <a href="{{ route('tours.show', $t->slug) }}" class="text-xs font-semibold uppercase tracking-[0.2em] text-brand underline decoration-accent decoration-2 underline-offset-8 hover:text-accent-text" aria-label="Discover {{ $t->name }} tours">Discover <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- 7. HOW IT WORKS: photo background under a deep navy veil, gold glows and an inset frame; three framed steps with Roman numerals --}}
<section id="how-it-works" class="section relative isolate scroll-mt-0 overflow-hidden bg-brand-dark text-white" aria-labelledby="how-title">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <img src="{{ asset('images/gallery/grossglockner.webp') }}" alt="" width="1600" height="1200" loading="lazy" class="photo-grade h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-brand-dark/90 via-brand-dark/65 to-brand-dark/90"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgb(15_59_89/0.85)_0%,transparent_65%)]"></div>
        <div class="absolute -left-40 bottom-0 size-[34rem] bg-[radial-gradient(closest-side,rgb(219_161_44/0.14),transparent)]"></div>
        <div class="absolute -right-40 top-0 size-[34rem] bg-[radial-gradient(closest-side,rgb(219_161_44/0.1),transparent)]"></div>
    </div>
    <span class="pointer-events-none absolute inset-4 -z-10 hidden border border-accent/30 sm:block lg:inset-8" aria-hidden="true"></span>
    <div class="container-page">
        <div class="mx-auto mb-16 max-w-4xl text-center lg:mb-24">
            <p class="flex items-center justify-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-10 bg-accent/70" aria-hidden="true"></span>How it works<span class="h-px w-10 bg-accent/70" aria-hidden="true"></span></p>
            <h2 id="how-title" class="mt-5 text-[32px] leading-[1.05] !text-white sm:text-[44px] lg:text-[56px]">Three Simple Steps to <em class="text-accent">Your Journey.</em></h2>
            <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-white/90">From first idea to the moment you arrive, we handle the details so you can enjoy the anticipation.</p>
        </div>

        <ol class="grid gap-14 md:grid-cols-3 md:gap-8 lg:gap-12">
            @foreach([
                ['I', 'mail', 'Tell us your plans', 'Share when, how and with whom you would like to travel. A few questions is all it takes.'],
                ['II', 'guide', 'Receive your personal quote', 'A local expert designs a tailored itinerary and price around your wishes.'],
                ['III', 'calendar', 'Travel with confidence', 'We confirm every detail and look after you from arrival to departure.'],
            ] as [$num, $ic, $t, $d])
                <li class="group relative">
                    <div class="relative h-full border border-white/20 bg-brand-dark/60 px-7 pb-10 pt-14 text-center shadow-[0_30px_60px_-30px_rgb(0_0_0/0.8)] transition duration-500 hover:-translate-y-1 hover:border-accent/60 sm:px-9 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                        <span class="pointer-events-none absolute inset-2 border border-accent/0 transition duration-500 group-hover:border-accent/30" aria-hidden="true"></span>
                        {{-- icon badge straddles the top edge of the frame --}}
                        <span class="absolute left-1/2 top-0 flex size-16 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-accent bg-brand-dark text-accent shadow-[0_0_0_6px_rgb(10_42_64/0.9)]" aria-hidden="true"><x-icon :name="$ic" :stroke="1.2" class="size-7" /></span>
                        <p class="font-display text-6xl italic leading-none text-accent/90 sm:text-7xl" aria-hidden="true">{{ $num }}</p>
                        <span class="mx-auto mt-5 block h-px w-12 bg-accent/70 transition-all duration-500 group-hover:w-24" aria-hidden="true"></span>
                        <h3 class="mt-6 text-2xl !text-white sm:text-3xl">{{ $t }}</h3>
                        <p class="mx-auto mt-4 max-w-xs leading-relaxed text-white/90">{{ $d }}</p>
                    </div>
                    @unless($loop->last)
                        <span class="absolute -right-[1.15rem] top-1/2 hidden -translate-y-1/2 text-[9px] text-accent md:block lg:-right-[1.65rem]" aria-hidden="true">&#9670;</span>
                    @endunless
                </li>
            @endforeach
        </ol>

        <div class="mt-16 flex flex-col items-center gap-4 text-center lg:mt-24">
            <x-button class="!px-9 !py-4 uppercase tracking-wider" @click="$store.modal.show({})">Begin Planning</x-button>
            <p class="text-sm text-white/90">No obligation. A personal reply from a local expert.</p>
        </div>
    </div>
</section>

{{-- 5. CURATED PACKAGES (6 per page, arrow + number pagination, ajax: resources/js/curated.js) --}}
@if($curatedPackages->isNotEmpty())
<section id="curated" class="section scroll-mt-24 bg-soft-white" aria-labelledby="curated-title">
    <div class="container-page">
        <div class="mb-10 flex flex-col gap-6 lg:mb-12 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="flex items-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-10 bg-accent" aria-hidden="true"></span>Curated packages</p>
                <h2 id="curated-title" class="mt-4 text-[32px] sm:text-[44px] lg:text-[56px]">Journeys We Have <em class="text-accent-text">Thoughtfully Curated.</em></h2>
                <p class="mt-5 max-w-xl text-brand">Itineraries that balance the sights worth seeing with time to enjoy them. Each one is a starting point we adapt to you.</p>
            </div>
            <a href="{{ route('packages.index') }}" class="inline-flex h-11 shrink-0 items-center justify-center self-start bg-cta px-9 text-xs font-medium uppercase tracking-[0.12em] text-brand transition hover:bg-cta-dark lg:self-auto">Explore Trips</a>
        </div>
        <div id="curated-list" data-endpoint="{{ route('packages.curated') }}" aria-live="polite">
            @include('partials.curated-packages', ['packages' => $curatedPackages])
        </div>
    </div>
</section>
@endif
{{-- 5b. TOP DESTINATIONS (bento of trip cards: text overlaid on the photo, places on one line with diamond separators). Plain navy background on purpose: the photos are the star. --}}
<section class="section bg-brand-dark text-white" aria-labelledby="destinations-title">
    <div class="container-page">
        <div class="mx-auto mb-12 max-w-3xl text-center lg:mb-16">
            <p class="flex items-center justify-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-10 bg-accent/70" aria-hidden="true"></span>Top destinations<span class="h-px w-10 bg-accent/70" aria-hidden="true"></span></p>
            <h2 id="destinations-title" class="mt-5 text-[32px] leading-[1.05] !text-white sm:text-[44px] lg:text-[56px]">From Alpine Peaks<br><em class="text-accent">To Imperial Streets.</em></h2>
            <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-white/90">Six ways to move through the country, from palaces on the Danube to lakeside villages and high Alpine passes. Each one is a starting point we shape around you.</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 lg:auto-rows-[20rem] lg:gap-6">
            @foreach([
                ['The Grand Austrian Journey', 'hero/hallstatt.webp', ['Vienna', 'Wachau', 'Salzburg', 'Hallstatt', 'Gosau', 'Innsbruck']],
                ['Imperial & Alpine', 'packages/tyrol-private-escorted.webp', ['Vienna', 'Melk', 'Linz', 'Salzburg', 'Zell am See', 'Innsbruck']],
                ['Lakeside Austria Escape', 'packages/alpine-lakes-self-drive.webp', ['Salzburg', 'St. Gilgen', 'St. Wolfgang', 'Hallstatt', 'Gosau', 'Bad Ischl']],
                ['Danube & Vineyards', 'packages/classic-austria-by-coach.webp', ['Vienna', 'Krems', 'Dürnstein', 'Melk', 'Linz', 'Salzburg']],
                ['The Alpine Discovery', 'packages/alpine-highlights-by-coach.webp', ['Innsbruck', 'Seefeld', 'Ötztal', 'Zell am See', 'Kaprun', 'Salzburg']],
                ['Castles & Countryside', 'hero/salzburg.webp', ['Vienna', 'Graz', 'Klagenfurt', 'Villach', 'Innsbruck', 'Salzburg']],
            ] as $n => [$title, $img, $places])
                {{-- Bento layout: the first journey is the large feature (2 columns x 2 rows from lg). Photo fills the card, text sits on a navy gradient. --}}
                @php $feature = $n === 0; @endphp
                <article class="group relative isolate flex min-h-[20rem] flex-col justify-end overflow-hidden border border-white/15 bg-brand-dark text-white shadow-[0_30px_60px_-25px_rgb(0_0_0/0.8)] transition duration-500 hover:border-accent/60 motion-reduce:transition-none {{ $feature ? 'sm:col-span-2 sm:min-h-[26rem] lg:row-span-2' : '' }}">
                    <img src="{{ asset('images/'.$img) }}" alt="" width="800" height="600" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover {{ $feature ? 'object-[50%_35%]' : '' }} transition duration-[1600ms] ease-out group-hover:scale-105 motion-reduce:transition-none">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-dark via-brand-dark/55 to-brand-dark/5" aria-hidden="true"></div>
                    <span class="pointer-events-none absolute inset-3 border border-accent/0 transition duration-500 group-hover:border-accent/60" aria-hidden="true"></span>
                    <span class="absolute left-6 top-5 font-display text-lg italic text-white/90" aria-hidden="true">0{{ $n + 1 }}</span>
                    <span class="absolute right-5 top-4 inline-flex size-10 items-center justify-center rounded-full border border-white/40 text-white transition duration-300 group-hover:border-accent group-hover:bg-accent group-hover:text-brand" aria-hidden="true">&nearr;</span>
                    <div class="p-6 {{ $feature ? 'sm:p-10' : 'sm:p-7' }}">
                        @if($feature)<p class="mb-3 flex items-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent [text-shadow:0_1px_10px_rgb(0_0_0/0.7)]"><span class="h-px w-8 bg-accent" aria-hidden="true"></span>Featured journey</p>@endif
                        <h3 class="leading-tight !text-white {{ $feature ? 'text-[32px] sm:text-[44px] lg:text-[56px]' : 'text-3xl' }}"><a href="{{ route('packages.index') }}" class="after:absolute after:inset-0">{{ $title }}</a></h3>
                        <ul class="mt-4 flex flex-wrap items-center gap-y-1 text-sm text-white/85 {{ $feature ? 'sm:text-base' : '' }}" aria-label="Places on {{ $title }}">
                            @foreach($places as $place)
                                <li class="flex items-center">{{ $place }}@unless($loop->last)<span class="px-2.5 text-[7px] text-accent" aria-hidden="true">&#9670;</span>@endunless</li>
                            @endforeach
                        </ul>
                        @if($feature)<span class="mt-7 inline-flex items-center gap-3 border-b-2 border-accent pb-1 text-xs font-semibold uppercase tracking-[0.25em] text-white" aria-hidden="true">Explore this journey <span class="text-accent transition group-hover:translate-x-1">&rarr;</span></span>@endif
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-12 text-center lg:mt-16">
            <x-button :href="route('packages.index')" class="!px-9 !py-4 uppercase tracking-wider">Explore trips</x-button>
        </div>
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
            <h2 id="planner-title" class="mt-4 text-[32px] sm:text-[44px]">{{ $site['planner_name'] }}</h2>
            @if(! empty($site['planner_role']))<p class="mt-2 text-sm font-medium uppercase tracking-wider text-accent-text">{{ $site['planner_role'] }}</p>@endif
            <div class="mt-6 max-w-xl space-y-4 text-muted">
                @foreach(preg_split('/\R{2,}/', trim($site['planner_bio'])) as $para)<p>{{ $para }}</p>@endforeach
            </div>
            <x-button class="mt-8" @click="$store.modal.show({})">Plan My Journey</x-button>
        </div>
    </div>
</section>
@endif

{{-- 8. GUEST STORIES: one large pull-quote, the rest smaller beneath it. No stars or third-party marks (not verified reviews). --}}
@if($testimonials->isNotEmpty())
@php $lead = $testimonials->first(); $others = $testimonials->slice(1); @endphp
<section id="guest-stories" class="section relative isolate scroll-mt-0 overflow-hidden bg-ivory" aria-labelledby="reviews-title">
    <p class="pointer-events-none absolute -left-6 -top-10 -z-10 select-none font-display text-[26rem] italic leading-none text-accent/15 lg:text-[40rem]" aria-hidden="true">&ldquo;</p>
    <div class="container-page">
        <div class="mx-auto max-w-4xl text-center">
            <p class="flex items-center justify-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-10 bg-accent" aria-hidden="true"></span>Guest stories<span class="h-px w-10 bg-accent" aria-hidden="true"></span></p>
            <h2 id="reviews-title" class="sr-only">What our travellers say</h2>
            <figure class="mt-10">
                <blockquote class="font-display text-3xl italic leading-[1.25] text-brand sm:text-4xl lg:text-[3.25rem]">&ldquo;{{ $lead->quote }}&rdquo;</blockquote>
                <figcaption class="mt-8 flex items-center justify-center gap-4 text-sm uppercase tracking-[0.2em] text-brand/85">
                    <span class="h-px w-8 bg-accent" aria-hidden="true"></span>
                    <span><strong class="font-semibold text-brand">{{ $lead->name }}</strong>@if($lead->location)<span class="text-accent" aria-hidden="true"> &#9670; </span>{{ $lead->location }}@endif</span>
                    <span class="h-px w-8 bg-accent" aria-hidden="true"></span>
                </figcaption>
            </figure>
        </div>
        @if($others->isNotEmpty())
        <div class="mx-auto mt-16 grid max-w-5xl gap-x-12 gap-y-10 border-t border-accent/40 pt-12 md:grid-cols-2 lg:mt-20 lg:pt-14">
            @foreach($others as $t)
                <figure class="flex flex-col {{ $loop->first ? '' : 'md:border-l md:border-line md:pl-12' }}">
                    <span class="font-display text-5xl leading-none text-accent" aria-hidden="true">&ldquo;</span>
                    <blockquote class="-mt-2 flex-1 font-display text-2xl italic leading-snug text-brand">{{ $t->quote }}</blockquote>
                    <figcaption class="mt-6 text-[14px] uppercase tracking-[0.2em] text-brand/85"><strong class="font-semibold text-brand">{{ $t->name }}</strong>@if($t->location)<span class="text-accent" aria-hidden="true"> &#9670; </span>{{ $t->location }}@endif</figcaption>
                </figure>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif

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
<section id="gallery" class="section relative isolate scroll-mt-24 overflow-hidden bg-brand-dark text-white" aria-labelledby="gallery-title"
    x-data="{ open: false, i: 0, active: 0, items: @js($galleryItems), tx: 0,
        show(n) { this.i = n; this.open = true },
        close() { this.open = false },
        next() { this.i = (this.i + 1) % this.items.length },
        prev() { this.i = (this.i - 1 + this.items.length) % this.items.length } }"
    @keydown.escape.window="close()" @keydown.arrow-right.window="open && next()" @keydown.arrow-left.window="open && prev()">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,rgb(15_59_89)_0%,transparent_60%)]" aria-hidden="true"></div>
    <div class="container-page">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="flex items-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-10 bg-accent/70" aria-hidden="true"></span>The gallery</p>
                <h2 id="gallery-title" class="mt-5 text-[32px] leading-[1.05] !text-white sm:text-[44px] lg:text-[56px]">Austria, <em class="text-accent">Frame By Frame.</em></h2>
            </div>
            <p class="max-w-md text-lg leading-relaxed text-white/90">A few of the places our journeys pass through. Tap a photo to see it larger.</p>
        </div>

        {{-- Desktop: cinematic accordion. The hovered/focused panel opens wide; click (or a second tap) opens the lightbox. --}}
        <div class="mt-14 hidden h-[36rem] gap-3 lg:flex xl:h-[40rem]">
            @foreach($gallery as $n => [$slug, $title, $caption, $tagline])
                <button type="button" @mouseenter="active = {{ $n }}" @focus="active = {{ $n }}" @click="active === {{ $n }} ? show({{ $n }}) : active = {{ $n }}"
                    aria-label="Open photo: {{ $title }}"
                    class="group relative isolate min-w-0 overflow-hidden bg-brand text-left transition-[flex-grow] duration-[800ms] ease-[cubic-bezier(0.22,1,0.36,1)] motion-reduce:transition-none"
                    :class="active === {{ $n }} ? 'grow-[6]' : 'grow'" style="flex-basis: 0">
                    <img src="{{ asset('images/gallery/'.$slug.'.webp') }}" alt="{{ $caption }}" width="1600" height="1200" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover transition duration-[1400ms] ease-out" :class="active === {{ $n }} ? 'scale-100' : 'scale-110 brightness-75'">
                    <span class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-dark/90 via-brand-dark/10 to-brand-dark/30" aria-hidden="true"></span>
                    <span class="pointer-events-none absolute inset-4 border border-accent/0 transition duration-700" :class="active === {{ $n }} && '!border-accent/70'" aria-hidden="true"></span>
                    <span class="absolute left-6 top-6 font-display text-lg italic text-white/90" aria-hidden="true">0{{ $n + 1 }}</span>
                    {{-- collapsed: vertical name --}}
                    <span class="absolute bottom-8 left-1/2 -translate-x-1/2 font-display text-2xl font-semibold uppercase tracking-[0.2em] text-white/90 transition-opacity duration-500 [writing-mode:vertical-rl] rotate-180" :class="active === {{ $n }} ? 'opacity-0' : 'opacity-100'" aria-hidden="true">{{ $title }}</span>
                    {{-- expanded: full caption --}}
                    <span class="absolute inset-x-0 bottom-0 block p-9 opacity-0 transition-opacity duration-500" :class="active === {{ $n }} && '!opacity-100 delay-300'">
                        <span class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.25em] text-accent"><span class="h-px w-8 bg-accent" aria-hidden="true"></span>{{ $tagline }}</span>
                        <span class="mt-3 block whitespace-nowrap font-display text-5xl font-semibold leading-none text-white">{{ $title }}</span>
                        <span class="mt-3 block max-w-sm text-white/90">{{ $caption }}</span>
                        <span class="mt-6 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.25em] text-white">View photo <x-icon name="search" :stroke="1.4" class="size-4 text-accent" /></span>
                    </span>
                </button>
            @endforeach
        </div>

        {{-- Mobile / tablet: swipeable tall cards --}}
        <div class="-mx-[15px] mt-10 flex snap-x snap-mandatory gap-3 overflow-x-auto px-[15px] pb-4 md:-mx-[60px] md:px-[60px] lg:hidden [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" tabindex="0" aria-label="Gallery photos, scroll sideways">
            @foreach($gallery as $n => [$slug, $title, $caption, $tagline])
                <button type="button" @click="show({{ $n }})" aria-label="Open photo: {{ $title }}" class="group relative isolate aspect-[3/4] w-[78%] max-w-sm shrink-0 snap-center overflow-hidden bg-brand text-left sm:w-[46%]">
                    <img src="{{ asset('images/gallery/'.$slug.($n === 0 || $n === 5 ? '' : '-thumb').'.webp') }}" alt="{{ $caption }}" width="800" height="1067" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover">
                    <span class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-dark/90 via-brand-dark/10 to-transparent" aria-hidden="true"></span>
                    <span class="pointer-events-none absolute inset-3 border border-accent/50" aria-hidden="true"></span>
                    <span class="absolute left-5 top-5 font-display text-base italic text-white/90" aria-hidden="true">0{{ $n + 1 }}</span>
                    <span class="absolute inset-x-0 bottom-0 block p-6">
                        <span class="flex items-center gap-2.5 text-xs font-semibold uppercase tracking-[0.22em] text-accent"><span class="h-px w-6 bg-accent" aria-hidden="true"></span>{{ $tagline }}</span>
                        <span class="mt-2 block font-display text-3xl font-semibold leading-tight text-white">{{ $title }}</span>
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
        <p class="absolute left-4 top-4 text-sm text-white/85 sm:left-8 sm:top-6" aria-live="polite"><span x-text="i + 1"></span> / <span x-text="items.length"></span></p>
        <button type="button" @click="close()" class="absolute right-3 top-3 rounded-full p-3 text-white/90 hover:bg-white/10 hover:text-white sm:right-6 sm:top-5" aria-label="Close gallery"><x-icon name="close" :stroke="1.5" class="size-7" /></button>

        <button type="button" @click="prev()" class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full p-3 text-white/90 hover:bg-white/10 hover:text-white sm:left-6" aria-label="Previous photo"><x-icon name="chevron" :stroke="1.5" class="size-8 rotate-90" /></button>
        <button type="button" @click="next()" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full p-3 text-white/90 hover:bg-white/10 hover:text-white sm:right-6" aria-label="Next photo"><x-icon name="chevron" :stroke="1.5" class="size-8 -rotate-90" /></button>

        <figure class="flex max-h-full max-w-5xl flex-col items-center" @click.self="close()">
            <img :src="items[i].src" :alt="items[i].caption" class="max-h-[75vh] w-auto max-w-full rounded-md object-contain shadow-2xl">
            <figcaption class="mt-4 text-center text-white">
                <span class="block font-display text-2xl" x-text="items[i].title"></span>
                <span class="block text-sm text-white/90" x-text="items[i].caption"></span>
            </figcaption>
        </figure>
    </div>
</section>

{{-- 8e. JOURNAL: editorial layout, one lead story + two supporting stories --}}
@if($posts->isNotEmpty())
@php $lead = $posts->first(); $others = $posts->slice(1); @endphp
<section class="section bg-ivory" aria-labelledby="journal-title">
    <div class="container-page">
        <div class="mb-12 flex flex-col gap-6 lg:mb-16 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="flex items-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-10 bg-accent" aria-hidden="true"></span>The Journal</p>
                <h2 id="journal-title" class="mt-5 text-[32px] leading-[1.05] sm:text-[44px] lg:text-[56px]">Austria travel stories and tips</h2>
                <p class="mt-5 max-w-xl text-lg leading-relaxed text-brand/85">Ideas, routes and practical advice for planning your trip.</p>
            </div>
            <a href="{{ route('journal.index') }}" class="shrink-0 self-start border-b-2 border-accent pb-1 text-xs font-semibold uppercase tracking-[0.25em] text-brand transition hover:text-accent-text lg:self-auto">Read the journal <span aria-hidden="true">&rarr;</span></a>
        </div>

        <div class="grid gap-8 lg:grid-cols-[7fr_5fr] lg:gap-10">
            {{-- Lead story --}}
            <article class="group relative isolate flex min-h-[26rem] flex-col justify-end overflow-hidden bg-brand-dark text-white lg:min-h-[36rem]">
                <img src="{{ $lead->imageUrl() }}" alt="{{ $lead->image_alt }}" width="1200" height="900" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover transition duration-[1400ms] ease-out group-hover:scale-105 motion-reduce:transition-none">
                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-dark/95 via-brand-dark/40 to-transparent" aria-hidden="true"></div>
                <span class="pointer-events-none absolute inset-4 border border-accent/0 transition duration-500 group-hover:border-accent/70 sm:inset-6" aria-hidden="true"></span>
                <div class="p-8 sm:p-12">
                    <p class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.25em] text-accent"><span class="h-px w-8 bg-accent" aria-hidden="true"></span>@if($lead->category)<span>{{ $lead->category }}</span><span aria-hidden="true">&middot;</span>@endif<time datetime="{{ $lead->published_at?->toDateString() }}" class="text-white/90">{{ $lead->published_at?->format('F j, Y') }}</time></p>
                    <h3 class="mt-4 max-w-xl text-3xl leading-tight !text-white sm:text-4xl lg:text-5xl"><a href="{{ route('journal.show', $lead) }}" class="after:absolute after:inset-0">{{ $lead->title }}</a></h3>
                    <p class="mt-4 max-w-lg text-white/90">{{ $lead->excerpt }}</p>
                    <span class="mt-6 inline-flex items-center gap-2 border-b border-accent pb-1 text-xs font-semibold uppercase tracking-[0.25em]" aria-hidden="true">Read article <span>&rarr;</span></span>
                </div>
            </article>

            {{-- Supporting stories --}}
            <div class="grid gap-8 lg:grid-rows-2 lg:gap-10">
                @foreach($others as $post)
                    <article class="group relative flex flex-col bg-white shadow-[0_24px_60px_-40px_rgb(10_42_64/0.5)] transition duration-500 hover:-translate-y-1 motion-reduce:transition-none motion-reduce:hover:translate-y-0 sm:flex-row">
                        <div class="relative aspect-[16/10] overflow-hidden bg-brand-soft sm:aspect-auto sm:w-2/5 sm:shrink-0">
                            <img src="{{ $post->imageUrl() }}" alt="{{ $post->image_alt }}" width="600" height="600" loading="lazy" class="photo-grade h-full w-full object-cover transition duration-[1200ms] ease-out group-hover:scale-105 motion-reduce:transition-none">
                        </div>
                        <div class="flex flex-1 flex-col border-t-2 border-accent p-6 sm:border-l-0 sm:border-t-0 sm:p-7 lg:border-t-2">
                            <p class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-accent-text">@if($post->category)<span>{{ $post->category }}</span><span aria-hidden="true">&middot;</span>@endif<time datetime="{{ $post->published_at?->toDateString() }}" class="font-medium text-brand/60">{{ $post->published_at?->format('M j, Y') }}</time></p>
                            <h3 class="mt-3 text-2xl leading-tight"><a href="{{ route('journal.show', $post) }}" class="after:absolute after:inset-0">{{ $post->title }}</a></h3>
                            <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-brand/85">{{ $post->excerpt }}</p>
                            <span class="mt-auto pt-5 text-xs font-semibold uppercase tracking-[0.25em] text-brand" aria-hidden="true">Read article <span class="text-accent-text">&rarr;</span></span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- 9. FAQ: heading + "still have a question" card on the left, accordion on the right --}}
@if($faqs->isNotEmpty())
<section class="section relative isolate overflow-hidden bg-soft-white" aria-labelledby="faq-title">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
        <div class="absolute -right-40 -top-40 size-[34rem] bg-[radial-gradient(closest-side,rgb(219_161_44/0.1),transparent)]"></div>
        <p class="absolute -left-6 top-4 select-none font-display text-[22rem] font-semibold italic leading-none text-brand/[0.03] lg:text-[30rem]">?</p>
    </div>
    <div class="container-page grid gap-12 lg:grid-cols-[4fr_8fr] lg:gap-24">
        <div class="lg:sticky lg:top-28 lg:self-start">
            <p class="flex items-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-10 bg-accent" aria-hidden="true"></span>FAQ</p>
            <h2 id="faq-title" class="mt-5 text-[32px] leading-[1.05] sm:text-[44px] lg:text-[56px]">Questions, <em class="text-accent-text">answered.</em></h2>
            <p class="mt-6 text-lg leading-relaxed text-brand/85">Everything you may wish to know before planning your journey to Austria.</p>
            <div class="mt-10 border border-accent/50 bg-white p-7 shadow-[0_24px_60px_-40px_rgb(10_42_64/0.5)]">
                <p class="font-display text-2xl font-semibold text-brand">Still have a question?</p>
                <p class="mt-2 text-sm leading-relaxed text-brand/85">Ask us anything. A member of our team will reply personally.</p>
                <x-button class="mt-5 !px-6 !py-3 uppercase tracking-wider" @click="$store.modal.show({})">Ask Our Team</x-button>
            </div>
        </div>
        <div class="border-t border-accent/50">@foreach($faqs as $f)<x-faq-item :question="$f->question" :number="sprintf('%02d', $loop->iteration)">{{ $f->answer }}</x-faq-item>@endforeach</div>
    </div>
</section>
@endif

{{-- 10. FINAL CTA: video background. Drop an MP4 (and optionally a WebM) at public/videos/austria-cta.mp4 / .webm; until then the still photo shows.
     The video is only fetched when the section scrolls into view, and never for reduced-motion or data-saver visitors. --}}
@php
    $ctaVideo = file_exists(public_path('videos/austria-cta.mp4')) ? asset('videos/austria-cta.mp4') : null;
    $ctaWebm = file_exists(public_path('videos/austria-cta.webm')) ? asset('videos/austria-cta.webm') : null;
@endphp
<section class="section relative isolate flex min-h-[32rem] items-center overflow-hidden bg-brand-dark text-center text-white lg:min-h-[40rem]"
    @if($ctaVideo)
    x-data="{ play: false }"
    x-init="if (! matchMedia('(prefers-reduced-motion: reduce)').matches && ! (navigator.connection && navigator.connection.saveData)) { new IntersectionObserver((e, o) => { if (e[0].isIntersecting) { play = true; o.disconnect() } }, { rootMargin: '300px' }).observe($el) }"
    @endif>
    <div class="absolute inset-0 -z-10" aria-hidden="true">
        <img src="{{ asset('images/hero/hallstatt.webp') }}" alt="" width="1920" height="747" loading="lazy" class="photo-grade h-full w-full object-cover">
        @if($ctaVideo)
            <template x-if="play">
                <video autoplay muted loop playsinline preload="none" poster="{{ asset('images/hero/hallstatt.webp') }}" class="absolute inset-0 h-full w-full object-cover">
                    @if($ctaWebm)<source src="{{ $ctaWebm }}" type="video/webm">@endif
                    <source src="{{ $ctaVideo }}" type="video/mp4">
                </video>
            </template>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-brand-dark/80 via-brand-dark/55 to-brand-dark/85"></div>
    </div>
    <span class="pointer-events-none absolute inset-4 -z-10 border border-accent/40 sm:inset-8" aria-hidden="true"></span>
    <div class="container-page max-w-3xl">
        <p class="flex items-center justify-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-10 bg-accent/70" aria-hidden="true"></span>Your Austrian journey<span class="h-px w-10 bg-accent/70" aria-hidden="true"></span></p>
        <h2 class="mt-5 text-[32px] leading-[1.05] !text-white sm:text-[44px] lg:text-[56px]">{{ $page->section('cta_title', 'Ready to see Austria?') }}</h2>
        <p class="mx-auto mt-5 max-w-xl text-lg text-white/90">{{ $page->section('cta_text') }}</p>
        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <x-button :href="route('contact')" @click.prevent="$store.modal.show({})" class="!px-8 !py-3.5 uppercase tracking-wider">Plan My Journey</x-button>
            @if(! empty($site['phone']))
                <x-button variant="light" :href="'tel:'.preg_replace('/[^\d+]/', '', ($site['phone_tel'] ?? '') ?: $site['phone'])" class="!px-8 !py-3.5">Call {{ $site['phone'] }}</x-button>
            @endif
        </div>
    </div>
</section>
@endsection

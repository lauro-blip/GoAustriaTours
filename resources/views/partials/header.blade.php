@php $tp = $transparentHeader ?? false; @endphp
<header @if($tp) class="relative transition-colors duration-500 ease-in-out motion-reduce:transition-none" :class="scrolled || menu || mega ? 'text-ink' : 'text-white'" @else class="sticky top-0 z-40 bg-soft-white/95 backdrop-blur border-b border-line" @endif x-data="{ menu: false, mega: '' }" @keydown.escape.window="menu = false; mega = ''" @mouseleave="mega = ''" @click.outside="mega = ''">
    @if($tp)
        {{-- Solid background fades in (opacity) instead of snapping --}}
        <div class="absolute inset-0 bg-soft-white/95 backdrop-blur border-b border-line shadow-sm transition-opacity duration-500 ease-in-out motion-reduce:transition-none" :class="scrolled || menu || mega ? 'opacity-100' : 'opacity-0'" aria-hidden="true"></div>
    @endif
    <div class="container-page relative flex items-center justify-between py-3">
        <a href="{{ route('home') }}" class="relative block font-display text-2xl sm:text-3xl font-semibold text-brand" aria-label="{{ $site['brand_name'] ?? 'Home' }} home">
            @if(! empty($site['logo']))
                <img src="{{ asset('storage/'.$site['logo']) }}" alt="{{ $site['brand_name'] ?? 'GoAustriaTours' }}" width="250" height="83" class="w-[180px] sm:w-[250px] max-w-full h-auto">
            @elseif($tp)
                {{-- Both logos stacked; they crossfade --}}
                <img src="{{ asset('images/logo-white.webp') }}" alt="{{ $site['brand_name'] ?? 'GoAustriaTours' }}" width="250" height="83" class="w-[180px] sm:w-[250px] max-w-full h-auto transition-opacity duration-500 ease-in-out motion-reduce:transition-none" :class="scrolled || menu || mega ? 'opacity-0' : 'opacity-100'">
                <img src="{{ asset('images/logo-navy.webp') }}" alt="" aria-hidden="true" width="250" height="83" class="absolute left-0 top-0 w-[180px] sm:w-[250px] max-w-full h-auto transition-opacity duration-500 ease-in-out motion-reduce:transition-none opacity-0" :class="scrolled || menu || mega ? 'opacity-100' : 'opacity-0'">
            @else
                <img src="{{ asset('images/logo-navy.webp') }}" alt="{{ $site['brand_name'] ?? 'GoAustriaTours' }}" width="250" height="83" class="w-[180px] sm:w-[250px] max-w-full h-auto">
            @endif
        </a>

        <nav aria-label="Main" class="hidden xl:flex items-center gap-5 2xl:gap-8 whitespace-nowrap text-sm font-medium">
            @if($navTourTypes->isNotEmpty())
            <button type="button" class="inline-flex items-center gap-1 hover:underline decoration-accent decoration-2 underline-offset-8" :aria-expanded="mega === 'tours'" aria-controls="mega-tours" @mouseenter="mega = 'tours'" @click="mega = mega === 'tours' ? '' : 'tours'">Tour Types <x-icon name="chevron" class="size-4 transition-transform duration-200" x-bind:class="mega === 'tours' && 'rotate-180'" /></button>
            @endif
            @if($navDestinations->isNotEmpty())
                <button type="button" class="inline-flex items-center gap-1 hover:underline decoration-accent decoration-2 underline-offset-8" :aria-expanded="mega === 'destinations'" aria-controls="mega-destinations" @mouseenter="mega = 'destinations'" @click="mega = mega === 'destinations' ? '' : 'destinations'">Destinations <x-icon name="chevron" class="size-4 transition-transform duration-200" x-bind:class="mega === 'destinations' && 'rotate-180'" /></button>
            @endif
            @if(count($navStyles))
                <button type="button" class="inline-flex items-center gap-1 hover:underline decoration-accent decoration-2 underline-offset-8" :aria-expanded="mega === 'style'" aria-controls="mega-style" @mouseenter="mega = 'style'" @click="mega = mega === 'style' ? '' : 'style'">Travel Style <x-icon name="chevron" class="size-4 transition-transform duration-200" x-bind:class="mega === 'style' && 'rotate-180'" /></button>
            @endif
            <button type="button" class="inline-flex items-center gap-1 hover:underline decoration-accent decoration-2 underline-offset-8" :aria-expanded="mega === 'vacations'" aria-controls="mega-vacations" @mouseenter="mega = 'vacations'" @click="mega = mega === 'vacations' ? '' : 'vacations'">Vacations &amp; Experiences <x-icon name="chevron" class="size-4 transition-transform duration-200" x-bind:class="mega === 'vacations' && 'rotate-180'" /></button>
            <button type="button" class="inline-flex items-center gap-1 hover:underline decoration-accent decoration-2 underline-offset-8" :aria-expanded="mega === 'about'" aria-controls="mega-about" @mouseenter="mega = 'about'" @click="mega = mega === 'about' ? '' : 'about'">About <x-icon name="chevron" class="size-4 transition-transform duration-200" x-bind:class="mega === 'about' && 'rotate-180'" /></button>
            <a href="{{ route('contact') }}" @mouseenter="mega = ''" class="hover:underline decoration-accent decoration-2 underline-offset-8">Contact</a>
        </nav>

        <button type="button" class="xl:hidden p-2 -mr-2" @click="menu = !menu" :aria-expanded="menu" aria-controls="mobile-menu" aria-label="Menu">
            <x-icon name="menu" class="size-7" />
        </button>
    </div>

    {{-- About mega menu (desktop): the story, plus Journal | Testimonials | Terms & Conditions | Privacy Policy. --}}
    <div id="mega-about" x-show="mega === 'about'" x-cloak x-transition.opacity.duration.150ms class="absolute inset-x-0 top-full z-40 hidden overflow-hidden border-t border-accent/50 bg-brand-dark text-white shadow-[0_40px_70px_-30px_rgb(0_0_0/0.7)] xl:block">
        <div class="pointer-events-none absolute -left-24 -top-24 size-[30rem] bg-[radial-gradient(closest-side,rgb(219_161_44/0.15),transparent)]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-32 bottom-[-8rem] size-[30rem] bg-[radial-gradient(closest-side,rgb(15_59_89/0.8),transparent)]" aria-hidden="true"></div>
        <div class="container-page relative grid gap-10 py-12 lg:grid-cols-[minmax(0,1fr)_repeat(2,minmax(0,1.35fr))] xl:gap-12">
            <div class="flex flex-col">
                <p class="flex items-center gap-3 whitespace-nowrap text-xs font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-8 shrink-0 bg-accent" aria-hidden="true"></span>About us</p>
                <p class="mt-5 font-display text-4xl font-medium leading-[1.05] text-white">{{ $site['brand_name'] ?? 'GoAustriaTours' }}</p>
                <p class="mt-4 text-[15px] leading-relaxed text-white/85">Austria, planned around you by people who know it. Read our story, or get in touch.</p>
                <div class="mt-auto pt-8">
                    <a href="{{ route('about') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-full bg-cta px-7 py-3 text-xs font-semibold uppercase tracking-wider text-brand transition hover:bg-cta-dark">Our story</a>
                    <a href="{{ route('contact') }}" class="mt-5 block w-fit whitespace-nowrap border-b-2 border-accent pb-1 text-xs font-semibold uppercase tracking-[0.2em] text-white hover:text-accent">Contact us <span aria-hidden="true">&rarr;</span></a>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:col-span-2">
                {{-- Journal --}}
                <a href="{{ route('journal.index') }}" class="group flex flex-col border border-white/15 bg-brand shadow-[0_30px_60px_-30px_rgb(0_0_0/0.8)] transition duration-500 hover:-translate-y-1 hover:border-accent/60 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                    @if($navPost)
                        <span class="relative isolate block aspect-[16/8] overflow-hidden bg-brand-dark">
                            <img src="{{ $navPost->imageUrl() }}" alt="" width="640" height="320" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover transition duration-[1400ms] ease-out group-hover:scale-105">
                            <span class="absolute inset-0 -z-10 bg-gradient-to-t from-brand/80 to-transparent" aria-hidden="true"></span>
                        </span>
                    @endif
                    <span class="flex flex-1 flex-col border-t-2 border-accent p-5">
                        <span class="font-display text-2xl font-medium text-white transition group-hover:text-accent">Journal</span>
                        <span class="mt-1 text-sm leading-relaxed text-white/85">Travel stories, routes and practical advice for planning Austria.</span>
                        @if($navPost)<span class="mt-3 line-clamp-1 text-xs uppercase tracking-wider text-accent">Latest: {{ $navPost->title }}</span>@endif
                    </span>
                </a>
                {{-- Testimonials --}}
                <a href="{{ route('testimonials') }}" class="group flex flex-col border border-white/15 bg-brand shadow-[0_30px_60px_-30px_rgb(0_0_0/0.8)] transition duration-500 hover:-translate-y-1 hover:border-accent/60 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                    <span class="flex flex-1 flex-col border-b-2 border-accent p-5 sm:border-b-0 sm:border-t-2 sm:order-2">
                        <span class="font-display text-2xl font-medium text-white transition group-hover:text-accent">Testimonials</span>
                        <span class="mt-1 text-sm leading-relaxed text-white/85">What travellers say about their journeys.</span>
                        <span class="mt-auto inline-flex items-center gap-2 pt-4 text-xs font-semibold uppercase tracking-[0.2em] text-accent">Read guest stories <span class="transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span></span>
                    </span>
                    @if($navStory)
                        <span class="relative flex flex-1 flex-col justify-center p-5 sm:order-1">
                            <span class="font-display text-5xl leading-none text-accent" aria-hidden="true">&ldquo;</span>
                            <span class="-mt-2 line-clamp-3 font-display text-xl italic leading-snug text-white">{{ $navStory->quote }}</span>
                            <span class="mt-3 text-xs uppercase tracking-wider text-white/75">{{ $navStory->name }}@if($navStory->location) &middot; {{ $navStory->location }}@endif</span>
                        </span>
                    @endif
                </a>
                {{-- Terms & Conditions --}}
                <a href="{{ route('terms') }}" class="group flex items-center justify-between gap-4 border border-white/15 bg-brand p-5 transition duration-500 hover:border-accent/60">
                    <span>
                        <span class="block font-display text-2xl font-medium text-white transition group-hover:text-accent">Terms &amp; Conditions</span>
                        <span class="mt-1 block text-sm text-white/85">The terms that apply when you book with us.</span>
                    </span>
                    <span class="text-accent transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                </a>
                {{-- Privacy Policy --}}
                <a href="{{ route('privacy') }}" class="group flex items-center justify-between gap-4 border border-white/15 bg-brand p-5 transition duration-500 hover:border-accent/60">
                    <span>
                        <span class="block font-display text-2xl font-medium text-white transition group-hover:text-accent">Privacy Policy</span>
                        <span class="mt-1 block text-sm text-white/85">How we collect, use and protect your information.</span>
                    </span>
                    <span class="text-accent transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Vacations & Experiences mega menu (desktop): Signature Collections | By Traveler | Featured Packages. --}}
    <div id="mega-vacations" x-show="mega === 'vacations'" x-cloak x-transition.opacity.duration.150ms class="absolute inset-x-0 top-full z-40 hidden overflow-hidden border-t border-accent/50 bg-brand-dark text-white shadow-[0_40px_70px_-30px_rgb(0_0_0/0.7)] xl:block">
        <div class="pointer-events-none absolute -right-24 -top-24 size-[30rem] bg-[radial-gradient(closest-side,rgb(219_161_44/0.15),transparent)]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -left-32 bottom-[-8rem] size-[30rem] bg-[radial-gradient(closest-side,rgb(15_59_89/0.8),transparent)]" aria-hidden="true"></div>
        <div class="container-page relative grid gap-12 py-12 lg:grid-cols-3 xl:gap-16">
            {{-- 1. Signature Collections --}}
            <div>
                <p class="flex items-center gap-3 whitespace-nowrap text-xs font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-8 shrink-0 bg-accent" aria-hidden="true"></span>Signature Collections</p>
                <ul class="mt-6 divide-y divide-white/15 border-y border-white/15">
                    @foreach($navCollections as $col)
                        <li>
                            <a href="{{ route('packages.index', ['collection' => $col['slug']]) }}" class="group flex items-center justify-between gap-4 py-4">
                                <span>
                                    <span class="block font-display text-2xl font-medium leading-tight text-white transition group-hover:text-accent">{{ $col['name'] }}</span>
                                    <span class="mt-1 block text-sm text-white/80">{{ $col['text'] }}</span>
                                </span>
                                <span class="shrink-0 text-xs font-semibold uppercase tracking-[0.2em] text-accent">{{ $col['count'] }} <span class="inline-block transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- 2. By Traveler (opens the enquiry form noting who is travelling) --}}
            <div>
                <p class="flex items-center gap-3 whitespace-nowrap text-xs font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-8 shrink-0 bg-accent" aria-hidden="true"></span>By Traveler</p>
                <ul class="mt-6 divide-y divide-white/15 border-y border-white/15">
                    @foreach(config('goaustria.traveler_types') as $slug => $tv)
                        <li>
                            <button type="button" @click="mega = ''; $store.modal.show({ traveler: @js($slug), traveler_label: @js($tv['label']) })" class="group flex w-full items-center justify-between gap-4 py-3.5 text-left">
                                <span>
                                    <span class="block font-display text-2xl font-medium leading-tight text-white transition group-hover:text-accent">{{ $tv['label'] }}</span>
                                    <span class="mt-0.5 block text-sm text-white/80">{{ $tv['text'] }}</span>
                                </span>
                                <span class="shrink-0 text-accent transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-4 text-sm text-white/75">Tell us who is travelling and a local expert will shape the journey.</p>
            </div>

            {{-- 3. Featured Packages --}}
            <div class="flex flex-col">
                <p class="flex items-center gap-3 whitespace-nowrap text-xs font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-8 shrink-0 bg-accent" aria-hidden="true"></span>Featured Packages</p>
                <ul class="mt-6 space-y-4">
                    @foreach($navFeatured as $fp)
                        <li>
                            <a href="{{ route('packages.show', $fp->slug) }}" class="group flex items-center gap-4">
                                <span class="relative size-16 shrink-0 overflow-hidden bg-brand">
                                    <img src="{{ $fp->hero_image ? asset('storage/'.$fp->hero_image) : asset('images/packages/'.$fp->slug.'.webp') }}" alt="" width="128" height="128" loading="lazy" class="photo-grade h-full w-full object-cover transition duration-700 group-hover:scale-110">
                                    <span class="pointer-events-none absolute inset-0 border border-accent/0 transition group-hover:border-accent/70" aria-hidden="true"></span>
                                </span>
                                <span>
                                    <span class="block font-display text-xl font-medium leading-snug text-white transition group-hover:text-accent">{{ $fp->title }}</span>
                                    @if($fp->nights)<span class="mt-0.5 block text-xs uppercase tracking-wider text-white/75">{{ $fp->nights }} nights</span>@endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('packages.index') }}" class="mt-auto inline-flex w-fit items-center gap-2 whitespace-nowrap border-b-2 border-accent pb-1 pt-6 text-xs font-semibold uppercase tracking-[0.2em] text-white hover:text-accent">View all packages <span class="text-accent" aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
    </div>

    {{-- Tour Types mega menu (desktop): dark navy panel with an intro column and one framed card per tour type (photo, summary, first packages). --}}
    @if($navTourTypes->isNotEmpty())
    <div id="mega-tours" x-show="mega === 'tours'" x-cloak x-transition.opacity.duration.150ms class="absolute inset-x-0 top-full z-40 hidden overflow-hidden border-t border-accent/50 bg-brand-dark text-white shadow-[0_40px_70px_-30px_rgb(0_0_0/0.7)] xl:block">
        <div class="pointer-events-none absolute -left-24 -top-24 size-[30rem] bg-[radial-gradient(closest-side,rgb(219_161_44/0.16),transparent)]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-32 bottom-[-8rem] size-[30rem] bg-[radial-gradient(closest-side,rgb(15_59_89/0.8),transparent)]" aria-hidden="true"></div>
        <div class="container-page relative grid gap-10 py-12 lg:grid-cols-[minmax(0,1.1fr)_repeat(3,minmax(0,1.3fr))] xl:gap-10">
            <div class="flex flex-col">
                <p class="flex items-center gap-3 whitespace-nowrap text-xs font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-8 shrink-0 bg-accent" aria-hidden="true"></span>Ways to travel</p>
                <p class="mt-5 font-display text-4xl font-medium leading-[1.05] text-white">Choose how you<br><em class="text-accent">see Austria.</em></p>
                <p class="mt-5 text-[15px] leading-relaxed text-white/85">Three ways to explore, each shaped around you. Not sure which suits you? Tell us and we will advise.</p>
                <div class="mt-auto pt-8">
                    <button type="button" @click="mega = ''; $store.modal.show({})" class="inline-flex items-center justify-center whitespace-nowrap rounded-full bg-cta px-7 py-3 text-xs font-semibold uppercase tracking-wider text-brand transition hover:bg-cta-dark">Plan my journey</button>
                    <a href="{{ route('packages.index') }}" class="mt-5 block w-fit whitespace-nowrap border-b-2 border-accent pb-1 text-xs font-semibold uppercase tracking-[0.2em] text-white hover:text-accent">Compare all packages <span aria-hidden="true">&rarr;</span></a>
                </div>
            </div>

            @foreach($navTourTypes as $t)
                <div class="group flex flex-col border border-white/15 bg-brand shadow-[0_30px_60px_-30px_rgb(0_0_0/0.8)] transition duration-500 hover:-translate-y-1 hover:border-accent/60 motion-reduce:transition-none motion-reduce:hover:translate-y-0">
                    <a href="{{ route('tours.show', $t->slug) }}" class="relative isolate block aspect-[16/10] overflow-hidden bg-brand-dark">
                        <img src="{{ $t->image ? asset('storage/'.$t->image) : asset('images/tours/'.$t->slug.'.webp') }}" alt="{{ $t->image_alt ?: $t->name.' tours of Austria' }}" width="960" height="660" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover transition duration-[1600ms] ease-out group-hover:scale-105">
                        <span class="absolute inset-0 -z-10 bg-gradient-to-t from-brand/90 via-transparent to-brand-dark/25" aria-hidden="true"></span>
                        <span class="pointer-events-none absolute inset-3 border border-accent/0 transition duration-500 group-hover:border-accent/70" aria-hidden="true"></span>
                        <span class="absolute left-5 top-3 font-display text-xl italic text-white/90" aria-hidden="true">{{ ['I', 'II', 'III', 'IV', 'V'][$loop->index] ?? $loop->iteration }}</span>
                    </a>
                    <div class="flex flex-1 flex-col border-t-2 border-accent p-6">
                        @if(filled($t->tagline))<p class="text-xs font-semibold uppercase tracking-[0.25em] text-accent">{{ $t->tagline }}</p>@endif
                        <a href="{{ route('tours.show', $t->slug) }}" class="mt-2 block font-display text-3xl font-medium leading-tight text-white">{{ $t->name }} <em class="text-accent">Tours</em></a>
                        @if(filled($t->summary))<p class="mt-3 line-clamp-3 text-sm leading-relaxed text-white/85">{{ $t->summary }}</p>@endif
                        @if($t->menuPackages->isNotEmpty())
                            <ul class="mt-4 space-y-2 border-t border-white/15 pt-4">
                                @foreach($t->menuPackages as $mp)
                                    <li><a href="{{ route('packages.show', $mp->slug) }}" class="group/p flex items-baseline justify-between gap-4 text-[15px] text-white hover:text-accent"><span class="underline decoration-transparent decoration-2 underline-offset-4 transition group-hover/p:decoration-accent">{{ $mp->title }}</span>@if($mp->nights)<span class="shrink-0 text-xs uppercase tracking-wider text-white/70">{{ $mp->nights }} nights</span>@endif</a></li>
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('packages.index', ['tour_type' => $t->slug]) }}" class="mt-auto inline-flex w-fit items-center gap-2 border-b-2 border-accent pb-1 pt-5 text-xs font-semibold uppercase tracking-[0.2em] text-white hover:text-accent">All {{ $t->name }} packages <span class="text-accent transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Travel Style mega menu (desktop): photo + heading on the left, a 3x2 grid of style cells on the right, tagline bar underneath. --}}
    @if(count($navStyles))
    <div id="mega-style" x-show="mega === 'style'" x-cloak x-transition.opacity.duration.150ms class="absolute inset-x-0 top-full z-40 hidden overflow-hidden border-t border-accent/50 bg-brand-dark text-white shadow-[0_40px_70px_-30px_rgb(0_0_0/0.7)] xl:block">
        <div class="grid xl:grid-cols-[minmax(0,22rem)_minmax(0,1fr)]">
            {{-- left: photo panel --}}
            <div class="relative isolate flex items-center justify-center overflow-hidden p-10 text-center">
                <img src="{{ asset('images/gallery/hallstatt-thumb.webp') }}" alt="" width="800" height="600" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover">
                <span class="absolute inset-0 -z-10 bg-gradient-to-b from-brand-dark/70 via-brand-dark/55 to-brand-dark/85" aria-hidden="true"></span>
                <div>
                    <p class="flex items-center justify-center gap-3 text-xs font-semibold uppercase tracking-[0.3em] text-accent"><span class="h-px w-8 bg-accent" aria-hidden="true"></span><span class="text-[8px]" aria-hidden="true">&#9670;</span>Travel style<span class="h-px w-8 bg-accent" aria-hidden="true"></span></p>
                    <p class="mt-5 font-display text-4xl font-medium leading-[1.05] text-white">Find the Austria that <em class="text-accent">suits you.</em></p>
                    <div class="my-6 flex items-center gap-3" aria-hidden="true">
                        <span class="h-px flex-1 bg-accent/60"></span>
                        <span class="flex size-10 items-center justify-center rounded-full border border-accent/70 text-accent"><x-icon name="flower" :stroke="1.3" class="size-5" /></span>
                        <span class="h-px flex-1 bg-accent/60"></span>
                    </div>
                    <p class="text-[15px] leading-relaxed text-white/90">Tell us what moves you, and we will shape the journey around it.</p>
                </div>
            </div>

            {{-- right: style cells + bottom bar --}}
            <div class="flex flex-col">
                <div class="grid flex-1 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($navStyles as $st)
                        <a href="{{ route('packages.index', ['style' => $st['slug']]) }}" class="group flex flex-col border-b border-l border-white/10 p-8 transition duration-300 hover:bg-white/[0.05]">
                            <span class="flex size-12 items-center justify-center rounded-full border border-accent/80 text-accent transition duration-300 group-hover:bg-accent group-hover:text-brand-dark"><x-icon :name="$st['icon']" :stroke="1.3" class="size-6" /></span>
                            <span class="mt-5 block font-display text-2xl font-medium leading-tight text-white">{{ $st['name'] }}</span>
                            <span class="mt-4 block h-px w-10 bg-accent/70 transition-all duration-500 group-hover:w-20" aria-hidden="true"></span>
                            <span class="mt-4 block text-[15px] leading-relaxed text-white/85">{{ $st['text'] }}</span>
                            <span class="mt-auto inline-flex items-center gap-3 pt-6 text-xs font-semibold uppercase tracking-[0.2em] text-white">Discover <span class="transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span></span>
                        </a>
                    @endforeach
                </div>
                <div class="flex items-center justify-between gap-6 border-l border-t border-white/10 px-8 py-4">
                    <p class="flex items-center gap-4 text-xs font-semibold uppercase tracking-[0.2em] text-white/90">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full border border-accent/70 text-accent" aria-hidden="true"><x-icon name="star" :stroke="1.3" class="size-4" /></span>
                        Every journey is shaped around you.
                    </p>
                    <a href="{{ route('packages.index') }}" class="inline-flex shrink-0 items-center gap-3 border border-accent px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-accent hover:text-brand-dark">Explore all journeys <span aria-hidden="true">&rarr;</span></a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Destinations mega menu (desktop). Opens on hover/click of "Destinations", closes when the pointer leaves the header, on Esc or on an outside click. --}}
    @if($navDestinations->isNotEmpty())
    <div id="mega-destinations" x-show="mega === 'destinations'" x-cloak x-transition.opacity.duration.150ms class="absolute inset-x-0 top-full z-40 hidden border-t border-line bg-soft-white text-ink shadow-[0_40px_60px_-30px_rgb(10_42_64/0.45)] xl:block">
        <div class="container-page grid gap-12 py-10 lg:grid-cols-3">
            @foreach($navDestinations as $d)
                <div>
                    <p class="flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-8 bg-accent" aria-hidden="true"></span>Destination</p>
                    <a href="{{ route('packages.index', ['destination' => $d->slug]) }}" class="mt-3 block font-display text-3xl font-medium leading-tight text-brand hover:text-accent-text">{{ $d->name }}</a>
                    @if(filled($d->summary))<p class="mt-2 max-w-xs text-sm leading-relaxed text-brand/85">{{ $d->summary }}</p>@endif
                    <ul class="mt-5 space-y-2.5 border-t border-line pt-5">
                        @foreach(config('goaustria.nav_places.'.$d->slug, []) as [$label, $term])
                            <li><a href="{{ route('packages.index', ['q' => $term]) }}" class="group inline-flex items-center gap-3 text-base text-brand hover:text-accent-text"><span class="text-[7px] text-accent" aria-hidden="true">&#9670;</span><span class="underline decoration-transparent decoration-2 underline-offset-4 transition group-hover:decoration-accent">{{ $label }}</span></a></li>
                        @endforeach
                    </ul>
                    <a href="{{ route('packages.index', ['destination' => $d->slug]) }}" class="mt-6 inline-flex items-center gap-2 border-b-2 border-accent pb-1 text-xs font-semibold uppercase tracking-[0.2em] text-brand hover:text-accent-text">All {{ $d->name }} trips <span aria-hidden="true">&rarr;</span></a>
                </div>
            @endforeach
            <a href="{{ route('packages.index') }}" class="group relative isolate hidden min-h-[17rem] flex-col justify-end overflow-hidden bg-brand-dark p-7 text-white lg:flex">
                <img src="{{ asset('images/gallery/hallstatt-thumb.webp') }}" alt="" width="800" height="600" loading="lazy" class="photo-grade absolute inset-0 -z-10 h-full w-full object-cover transition duration-[1400ms] ease-out group-hover:scale-105">
                <span class="absolute inset-0 -z-10 bg-gradient-to-t from-brand-dark via-brand-dark/50 to-brand-dark/10" aria-hidden="true"></span>
                <span class="pointer-events-none absolute inset-3 border border-accent/0 transition duration-500 group-hover:border-accent/60" aria-hidden="true"></span>
                <span class="font-display text-3xl italic leading-tight">Not sure where to start?</span>
                <span class="mt-2 text-sm text-white/90">Browse every journey, or tell us what you love and we will shape one for you.</span>
                <span class="mt-4 inline-flex items-center gap-2 border-b-2 border-accent pb-1 text-xs font-semibold uppercase tracking-[0.2em] self-start">View all packages <span aria-hidden="true">&rarr;</span></span>
            </a>
        </div>
    </div>
    @endif

    <nav id="mobile-menu" aria-label="Mobile" x-show="menu" x-cloak class="xl:hidden border-t border-line bg-soft-white text-ink">
        <ul class="container-page max-h-[calc(100svh-5rem)] divide-y divide-line overflow-y-auto py-2 text-base">
            <li><a class="block py-3.5" href="{{ route('home') }}">Home</a></li>
            @foreach($navTourTypes as $t)
                <li><a class="block py-3.5" href="{{ route('tours.show', $t->slug) }}">{{ $t->name }} Tours</a></li>
            @endforeach
            @if($navDestinations->isNotEmpty())
                <li x-data="{ dd: false }">
                    <button type="button" class="flex w-full items-center justify-between py-3.5 text-left" :aria-expanded="dd" @click="dd = !dd">Destinations <x-icon name="chevron" class="size-5 transition-transform duration-200" x-bind:class="dd && 'rotate-180'" /></button>
                    <div x-show="dd" x-cloak class="pb-3 pl-3">
                        @foreach($navDestinations as $d)
                            <a href="{{ route('packages.index', ['destination' => $d->slug]) }}" class="block py-2.5 font-display text-xl font-medium text-brand">{{ $d->name }}</a>
                            <ul class="mb-2 border-l border-accent/50 pl-4">
                                @foreach(config('goaustria.nav_places.'.$d->slug, []) as [$label, $term])
                                    <li><a class="block py-2 text-[15px] text-brand/90" href="{{ route('packages.index', ['q' => $term]) }}">{{ $label }}</a></li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </li>
            @endif
            @if(count($navStyles))
                <li x-data="{ ts: false }">
                    <button type="button" class="flex w-full items-center justify-between py-3.5 text-left" :aria-expanded="ts" @click="ts = !ts">Travel Style <x-icon name="chevron" class="size-5 transition-transform duration-200" x-bind:class="ts && 'rotate-180'" /></button>
                    <ul x-show="ts" x-cloak class="mb-2 border-l border-accent/50 pl-4">
                        @foreach($navStyles as $st)
                            <li><a class="block py-2.5 text-[15px] text-brand/90" href="{{ route('packages.index', ['style' => $st['slug']]) }}">{{ $st['name'] }}</a></li>
                        @endforeach
                    </ul>
                </li>
            @endif
            <li x-data="{ vx: false }">
                <button type="button" class="flex w-full items-center justify-between py-3.5 text-left" :aria-expanded="vx" @click="vx = !vx">Vacations &amp; Experiences <x-icon name="chevron" class="size-5 transition-transform duration-200" x-bind:class="vx && 'rotate-180'" /></button>
                <div x-show="vx" x-cloak class="pb-3 pl-3">
                    <p class="pt-1 text-xs font-semibold uppercase tracking-[0.2em] text-accent-text">Signature Collections</p>
                    <ul class="mb-3 border-l border-accent/50 pl-4">
                        @foreach($navCollections as $col)<li><a class="block py-2 text-[15px] text-brand/90" href="{{ route('packages.index', ['collection' => $col['slug']]) }}">{{ $col['name'] }}</a></li>@endforeach
                    </ul>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-text">By Traveler</p>
                    <ul class="mb-3 border-l border-accent/50 pl-4">
                        @foreach(config('goaustria.traveler_types') as $slug => $tv)<li><button type="button" class="block py-2 text-left text-[15px] text-brand/90" @click="menu = false; $store.modal.show({ traveler: @js($slug), traveler_label: @js($tv['label']) })">{{ $tv['label'] }}</button></li>@endforeach
                    </ul>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-accent-text">Featured Packages</p>
                    <ul class="border-l border-accent/50 pl-4">
                        @foreach($navFeatured as $fp)<li><a class="block py-2 text-[15px] text-brand/90" href="{{ route('packages.show', $fp->slug) }}">{{ $fp->title }}</a></li>@endforeach
                    </ul>
                </div>
            </li>
            <li x-data="{ ab: false }">
                <button type="button" class="flex w-full items-center justify-between py-3.5 text-left" :aria-expanded="ab" @click="ab = !ab">About <x-icon name="chevron" class="size-5 transition-transform duration-200" x-bind:class="ab && 'rotate-180'" /></button>
                <ul x-show="ab" x-cloak class="mb-2 border-l border-accent/50 pl-4">
                    <li><a class="block py-2.5 text-[15px] text-brand/90" href="{{ route('about') }}">About us</a></li>
                    <li><a class="block py-2.5 text-[15px] text-brand/90" href="{{ route('journal.index') }}">Journal</a></li>
                    <li><a class="block py-2.5 text-[15px] text-brand/90" href="{{ route('testimonials') }}">Testimonials</a></li>
                    <li><a class="block py-2.5 text-[15px] text-brand/90" href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                    <li><a class="block py-2.5 text-[15px] text-brand/90" href="{{ route('privacy') }}">Privacy Policy</a></li>
                </ul>
            </li>
            <li><a class="block py-3.5" href="{{ route('contact') }}">Contact</a></li>
            <li class="py-4"><x-button class="w-full" @click="menu = false; $store.modal.show({})">Plan My Journey</x-button></li>
        </ul>
    </nav>
</header>

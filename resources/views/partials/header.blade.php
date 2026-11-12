<header class="sticky top-0 z-40 bg-ivory/95 backdrop-blur border-b border-line" x-data="{ menu: false }">
    <div class="container-page flex items-center justify-between py-3">
        <a href="{{ route('home') }}" class="font-display text-2xl sm:text-3xl font-semibold text-brand" aria-label="{{ $site['brand_name'] ?? 'Home' }} home">
            @if(! empty($site['logo']))
                <img src="{{ asset('storage/'.$site['logo']) }}" alt="{{ $site['brand_name'] }}" class="h-10 w-auto">
            @else
                Go<span class="text-accent-text">Austria</span>Tours
            @endif
        </a>

        <nav aria-label="Main" class="hidden lg:flex items-center gap-8 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-cta">Home</a>
            <div class="relative" x-data="{ o: false }" @mouseenter="o = true" @mouseleave="o = false" @keydown.escape="o = false">
                <button type="button" class="inline-flex items-center gap-1 hover:text-cta" :aria-expanded="o" @click="o = !o">Tour Types <x-icon name="chevron" class="size-4" /></button>
                <div x-show="o" x-cloak class="absolute left-0 top-full pt-2">
                    <ul class="min-w-48 rounded-xl bg-white border border-line shadow-card py-2">
                        @foreach($navTourTypes as $t)
                            <li><a href="{{ route('tours.show', $t->slug) }}" class="block px-4 py-2 hover:bg-brand-soft">{{ $t->name }} Tours</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <a href="{{ route('packages.index') }}" class="hover:text-cta">Packages</a>
            <a href="{{ route('about') }}" class="hover:text-cta">About</a>
            <a href="{{ route('contact') }}" class="hover:text-cta">Contact</a>
        </nav>

        <button type="button" class="lg:hidden p-2 -mr-2" @click="menu = !menu" :aria-expanded="menu" aria-controls="mobile-menu" aria-label="Menu">
            <x-icon name="menu" class="size-7" />
        </button>
    </div>

    <nav id="mobile-menu" aria-label="Mobile" x-show="menu" x-cloak class="lg:hidden border-t border-line bg-ivory">
        <ul class="container-page py-3 space-y-1 text-base">
            <li><a class="block py-2" href="{{ route('home') }}">Home</a></li>
            @foreach($navTourTypes as $t)
                <li><a class="block py-2" href="{{ route('tours.show', $t->slug) }}">{{ $t->name }} Tours</a></li>
            @endforeach
            <li><a class="block py-2" href="{{ route('packages.index') }}">Packages</a></li>
            <li><a class="block py-2" href="{{ route('about') }}">About</a></li>
            <li><a class="block py-2" href="{{ route('contact') }}">Contact</a></li>
        </ul>
    </nav>
</header>

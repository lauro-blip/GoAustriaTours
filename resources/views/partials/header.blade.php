@php $tp = $transparentHeader ?? false; @endphp
<header @if($tp) class="relative transition-colors duration-500 ease-in-out motion-reduce:transition-none" :class="scrolled || menu ? 'text-ink' : 'text-white'" @else class="sticky top-0 z-40 bg-soft-white/95 backdrop-blur border-b border-line" @endif x-data="{ menu: false }">
    @if($tp)
        {{-- Solid background fades in (opacity) instead of snapping --}}
        <div class="absolute inset-0 bg-soft-white/95 backdrop-blur border-b border-line shadow-sm transition-opacity duration-500 ease-in-out motion-reduce:transition-none" :class="scrolled || menu ? 'opacity-100' : 'opacity-0'" aria-hidden="true"></div>
    @endif
    <div class="container-page relative flex items-center justify-between py-3">
        <a href="{{ route('home') }}" class="relative block font-display text-2xl sm:text-3xl font-semibold text-brand" aria-label="{{ $site['brand_name'] ?? 'Home' }} home">
            @if(! empty($site['logo']))
                <img src="{{ asset('storage/'.$site['logo']) }}" alt="{{ $site['brand_name'] ?? 'GoAustriaTours' }}" width="250" height="83" class="w-[250px] max-w-full h-auto">
            @elseif($tp)
                {{-- Both logos stacked; they crossfade --}}
                <img src="{{ asset('images/logo-white.png') }}" alt="{{ $site['brand_name'] ?? 'GoAustriaTours' }}" width="250" height="83" class="w-[250px] max-w-full h-auto transition-opacity duration-500 ease-in-out motion-reduce:transition-none" :class="scrolled || menu ? 'opacity-0' : 'opacity-100'">
                <img src="{{ asset('images/logo-navy.png') }}" alt="" aria-hidden="true" width="250" height="83" class="absolute left-0 top-0 w-[250px] max-w-full h-auto transition-opacity duration-500 ease-in-out motion-reduce:transition-none opacity-0" :class="scrolled || menu ? 'opacity-100' : 'opacity-0'">
            @else
                <img src="{{ asset('images/logo-navy.png') }}" alt="{{ $site['brand_name'] ?? 'GoAustriaTours' }}" width="250" height="83" class="w-[250px] max-w-full h-auto">
            @endif
        </a>

        <nav aria-label="Main" class="hidden lg:flex items-center gap-8 text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:underline decoration-accent decoration-2 underline-offset-8">Home</a>
            <div class="relative" x-data="{ o: false }" @mouseenter="o = true" @mouseleave="o = false" @keydown.escape="o = false">
                <button type="button" class="inline-flex items-center gap-1 hover:underline decoration-accent decoration-2 underline-offset-8" :aria-expanded="o" @click="o = !o">Tour Types <x-icon name="chevron" class="size-4" /></button>
                <div x-show="o" x-cloak class="absolute left-0 top-full pt-2">
                    <ul class="min-w-48 rounded-xl bg-white text-ink border border-line shadow-card py-2">
                        @foreach($navTourTypes as $t)
                            <li><a href="{{ route('tours.show', $t->slug) }}" class="block px-4 py-2 hover:bg-brand-soft">{{ $t->name }} Tours</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <a href="{{ route('packages.index') }}" class="hover:underline decoration-accent decoration-2 underline-offset-8">Packages</a>
            <a href="{{ route('journal.index') }}" class="hover:underline decoration-accent decoration-2 underline-offset-8">Journal</a>
            <a href="{{ route('about') }}" class="hover:underline decoration-accent decoration-2 underline-offset-8">About</a>
            <a href="{{ route('contact') }}" class="hover:underline decoration-accent decoration-2 underline-offset-8">Contact</a>
        </nav>

        <button type="button" class="lg:hidden p-2 -mr-2" @click="menu = !menu" :aria-expanded="menu" aria-controls="mobile-menu" aria-label="Menu">
            <x-icon name="menu" class="size-7" />
        </button>
    </div>

    <nav id="mobile-menu" aria-label="Mobile" x-show="menu" x-cloak class="lg:hidden border-t border-line bg-soft-white text-ink">
        <ul class="container-page py-3 space-y-1 text-base">
            <li><a class="block py-2" href="{{ route('home') }}">Home</a></li>
            @foreach($navTourTypes as $t)
                <li><a class="block py-2" href="{{ route('tours.show', $t->slug) }}">{{ $t->name }} Tours</a></li>
            @endforeach
            <li><a class="block py-2" href="{{ route('packages.index') }}">Packages</a></li>
            <li><a class="block py-2" href="{{ route('journal.index') }}">Journal</a></li>
            <li><a class="block py-2" href="{{ route('about') }}">About</a></li>
            <li><a class="block py-2" href="{{ route('contact') }}">Contact</a></li>
        </ul>
    </nav>
</header>

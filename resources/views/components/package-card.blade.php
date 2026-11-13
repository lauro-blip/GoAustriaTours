@props(['package'])
@php
    $img = $package->hero_image ? asset('storage/'.$package->hero_image) : asset('images/packages/'.$package->slug.'.webp');
    $alt = $package->hero_image_alt ?: $package->title;
    // Places for the "Itinerary" line: the text after the colon in each itinerary day title ("Days 1-3: Vienna" -> "Vienna").
    $places = collect($package->itinerary ?? [])
        ->map(fn ($d) => trim(\Illuminate\Support\Str::after($d['title'] ?? '', ':')))
        ->filter()->unique()->take(4)->implode(', ') ?: ($package->destination?->name ?? '');
@endphp
<x-card class="flex h-full flex-col !rounded-sm !border-accent/25">
    <div class="relative">
        <a href="{{ route('packages.show', $package) }}" tabindex="-1" aria-hidden="true" class="block aspect-[437/201] overflow-hidden bg-brand-soft">
            <img src="{{ $img }}" alt="{{ $alt }}" width="800" height="368" loading="lazy" class="h-full w-full object-cover transition duration-700 hover:scale-105">
        </a>
        {{-- Save-for-later heart: remembered in this browser only --}}
        <button type="button" class="absolute right-3.5 top-3.5 flex size-[30px] items-center justify-center rounded-full bg-white text-brand shadow transition hover:text-cta-dark"
            x-data="{ id: {{ $package->id }}, saved: false,
                init() { try { this.saved = JSON.parse(localStorage.getItem('savedPackages') || '[]').includes(this.id) } catch (e) {} },
                toggle() { this.saved = ! this.saved; try { const s = new Set(JSON.parse(localStorage.getItem('savedPackages') || '[]')); this.saved ? s.add(this.id) : s.delete(this.id); localStorage.setItem('savedPackages', JSON.stringify([...s])) } catch (e) {} } }"
            @click="toggle()" :aria-pressed="saved" aria-label="Save {{ $package->title }} for later">
            <svg class="size-4 transition" :class="saved && 'text-cta-dark'" viewBox="0 0 24 24" :fill="saved ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20.5s-7.5-4.6-7.5-10.2A4.3 4.3 0 0 1 12 7.600a4.300 4.300 0 0 1 7.500 2.700c0 5.600-7.500 10.200-7.500 10.200z"/></svg>
        </button>
    </div>

    <div class="flex flex-1 flex-col px-[18px] pb-[22px] pt-5">
        <div class="flex items-center justify-between gap-3 text-[11px] uppercase">
            <span>{{ $package->tourType?->name }}</span>
            <span class="flex items-center gap-1.5"><x-icon name="calendar" :stroke="1.4" class="size-[15px] text-accent-text" />{{ $package->nights }} nights</span>
        </div>
        <h3 class="mt-2.5 text-2xl font-bold leading-tight"><a href="{{ route('packages.show', $package) }}">{{ $package->title }}</a></h3>

        @if($places)
            <p class="mt-3.5 text-xs font-bold uppercase">Itinerary:</p>
            <p class="mt-2.5 flex items-start gap-2 text-[13px] leading-5"><x-icon name="pin" :stroke="1.3" class="mt-0.5 size-4 shrink-0" /><span>{{ $places }}</span></p>
        @endif

        <a href="{{ route('packages.show', $package) }}" class="mt-8 self-start border-b border-brand pb-0.5 text-[10px] font-medium uppercase tracking-[0.2em] hover:border-accent hover:text-accent-text" aria-label="Explore {{ $package->title }}">Explore tour</a>

        <div class="mt-auto pt-6"><div class="flex items-end justify-between gap-3 border-t border-accent/30 pt-7">
            <div>
                <p class="text-[10px] uppercase tracking-wider">Starting from</p>
                <p class="mt-1 leading-none"><strong class="text-xl font-semibold text-brand">{{ $package->price_from ? '$'.number_format($package->price_from) : '[CONFIRM: price]' }}</strong>@if($package->price_from)<span class="text-xs">pp</span>@endif</p>
            </div>
            <x-button @click="$store.modal.show({ package_id: {{ $package->id }} })" class="!h-10 !rounded-sm !px-[14px] !py-0 !text-[11px] uppercase tracking-wide" aria-label="Get a quote for {{ $package->title }}">Get a Quote</x-button>
        </div></div>
    </div>
</x-card>

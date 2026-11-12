@props(['package'])
<x-card class="flex flex-col">
    <div class="aspect-[4/3] bg-brand-soft flex items-center justify-center text-xs text-muted">
        @if($package->hero_image)
            <img src="{{ asset('storage/'.$package->hero_image) }}" alt="{{ $package->hero_image_alt }}" width="800" height="600" loading="lazy" class="h-full w-full object-cover">
        @else
            [PLACEHOLDER IMAGE]
        @endif
    </div>
    <div class="flex flex-1 flex-col p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-accent-text">{{ $package->tourType?->name }}@if($package->destination) &middot; {{ $package->destination->name }}@endif</p>
        <h3 class="mt-1 text-2xl"><a href="{{ route('packages.show', $package) }}">{{ $package->title }}</a></h3>
        <p class="mt-1 text-sm text-muted">{{ $package->nights }} nights</p>
        <p class="mt-3 text-sm text-muted">{{ $package->summary }}</p>
        <div class="mt-auto flex items-end justify-between pt-5">
            <p class="text-sm">From <strong class="text-xl text-brand">{{ $package->price_from ? '$'.number_format($package->price_from) : '[CONFIRM: price]' }}</strong></p>
            <x-button variant="outline" :href="route('packages.show', $package)" class="!px-4 !py-2">View</x-button>
        </div>
    </div>
</x-card>

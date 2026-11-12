@extends('layouts.app', [
    'metaTitle' => 'Austria Tour Packages | '.($site['brand_name'] ?? ''),
    'metaDescription' => 'Browse self drive, escorted and coach tour packages in Austria.',
    'canonical' => route('packages.index'),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Packages', route('packages.index')]]), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
<section class="section">
    <div class="container-page">
        <x-section-heading tag="h1" title="Austria tour packages" subtitle="Filter by tour type or destination." />

        <form method="GET" action="{{ route('packages.index') }}" class="mb-10 flex flex-wrap items-end justify-center gap-4">
            <x-form-field name="tour_type" type="select" label="Tour type" id="flt" :value="$activeTourType?->slug">
                <option value="">All tour types</option>
                @foreach($tourTypes as $t)<option value="{{ $t->slug }}" @selected($activeTourType?->id === $t->id)>{{ $t->name }}</option>@endforeach
            </x-form-field>
            <x-form-field name="destination" type="select" label="Destination" id="flt">
                <option value="">All destinations</option>
                @foreach($destinations as $d)<option value="{{ $d->slug }}" @selected($activeDestination?->id === $d->id)>{{ $d->name }}</option>@endforeach
            </x-form-field>
            <x-button type="submit" variant="secondary">Filter</x-button>
        </form>

        @forelse($packages as $p)
            @if($loop->first)<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@endif
            <x-package-card :package="$p" />
            @if($loop->last)</div>@endif
        @empty
            <p class="text-center text-muted">No packages match those filters. <a class="underline" href="{{ route('packages.index') }}">Clear filters</a></p>
        @endforelse
    </div>
</section>
@endsection

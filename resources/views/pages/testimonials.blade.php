@extends('layouts.app', [
    'metaTitle' => 'Guest Stories | What Our Travellers Say | '.($site['brand_name'] ?? ''),
    'metaDescription' => 'Read what travellers say about their journeys through Austria.',
    'canonical' => route('testimonials'),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Guest stories', route('testimonials')]]), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
<section class="section relative isolate overflow-hidden bg-ivory">
    <p class="pointer-events-none absolute -left-6 -top-10 -z-10 select-none font-display text-[26rem] italic leading-none text-accent/15 lg:text-[40rem]" aria-hidden="true">&ldquo;</p>
    <div class="container-page">
        <div class="mx-auto max-w-3xl text-center">
            <p class="flex items-center justify-center gap-3 text-[14px] font-semibold uppercase tracking-[0.3em] text-accent-text"><span class="h-px w-10 bg-accent" aria-hidden="true"></span>Guest stories<span class="h-px w-10 bg-accent" aria-hidden="true"></span></p>
            <h1 class="mt-5 text-[32px] leading-[1.05] sm:text-[44px] lg:text-[56px]">What Our <em class="text-accent-text">Travellers Say.</em></h1>
        </div>

        @forelse($stories as $t)
            @if($loop->first)<div class="mx-auto mt-14 grid max-w-5xl gap-x-12 gap-y-14 md:grid-cols-2">@endif
            <figure class="flex flex-col border-t border-accent/40 pt-8">
                <span class="font-display text-6xl leading-none text-accent" aria-hidden="true">&ldquo;</span>
                <blockquote class="-mt-2 flex-1 font-display text-2xl italic leading-snug text-brand sm:text-3xl">{{ $t->quote }}</blockquote>
                <figcaption class="mt-6 text-[14px] uppercase tracking-[0.2em] text-brand/85"><strong class="font-semibold text-brand">{{ $t->name }}</strong>@if($t->location)<span class="text-accent" aria-hidden="true"> &#9670; </span>{{ $t->location }}@endif</figcaption>
            </figure>
            @if($loop->last)</div>@endif
        @empty
            <p class="mt-12 text-center text-muted">Guest stories are coming soon.</p>
        @endforelse

        <div class="mt-16 text-center">
            <p class="font-display text-2xl italic text-brand">Your Austria, planned around you.</p>
            <x-button class="mt-6 !px-9 !py-4 uppercase tracking-wider" @click="$store.modal.show({})">Plan My Journey</x-button>
        </div>
    </div>
</section>
@endsection

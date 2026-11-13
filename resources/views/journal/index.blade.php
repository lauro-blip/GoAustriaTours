@extends('layouts.app', [
    'metaTitle' => 'Austria Travel Journal | '.($site['brand_name'] ?? ''),
    'metaDescription' => 'Travel tips, route ideas and destination guides for planning your trip to Austria.',
    'canonical' => route('journal.index'),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Journal', route('journal.index')]]), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
<section class="section">
    <div class="container-page">
        <x-section-heading tag="h1" eyebrow="The Journal" title="Austria travel stories and tips" subtitle="Ideas, routes and practical advice for planning your trip." />
        @forelse($posts as $post)
            @if($loop->first)<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">@endif
            <x-journal-card :post="$post" />
            @if($loop->last)</div>@endif
        @empty
            <p class="text-center text-muted">New articles are coming soon.</p>
        @endforelse
        <div class="mt-10">{{ $posts->links() }}</div>
    </div>
</section>
@endsection

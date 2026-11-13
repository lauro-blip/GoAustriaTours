@extends('layouts.app', [
    'metaTitle' => $post->meta_title ?: $post->title.' | '.($site['brand_name'] ?? ''),
    'metaDescription' => $post->meta_description ?: $post->excerpt,
    'canonical' => route('journal.show', $post),
    'ogImage' => $post->imageUrl(),
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Journal', route('journal.index')], [$post->title, route('journal.show', $post)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $post->title, 'description' => $post->excerpt, 'image' => $post->imageUrl(), 'datePublished' => $post->published_at?->toIso8601String(), 'dateModified' => $post->updated_at?->toIso8601String(), 'mainEntityOfPage' => route('journal.show', $post), 'publisher' => ['@type' => 'Organization', 'name' => $site['brand_name'] ?? '']], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
<article class="section">
    <div class="container-page max-w-3xl">
        <a href="{{ route('journal.index') }}" class="text-sm font-semibold text-brand underline-offset-4 hover:underline">&larr; Back to the journal</a>
        <p class="mt-6 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-accent-text">
            @if($post->category)<span>{{ $post->category }}</span><span aria-hidden="true">&middot;</span>@endif
            <time datetime="{{ $post->published_at?->toDateString() }}" class="font-medium normal-case tracking-normal text-muted">{{ $post->published_at?->format('F j, Y') }}</time>
            <span aria-hidden="true">&middot;</span><span class="font-medium normal-case tracking-normal text-muted">{{ $post->readingMinutes() }} min read</span>
        </p>
        <h1 class="mt-3 text-4xl sm:text-5xl">{{ $post->title }}</h1>
        @if($post->excerpt)<p class="mt-4 text-lg text-muted">{{ $post->excerpt }}</p>@endif
        <img src="{{ $post->imageUrl() }}" alt="{{ $post->image_alt }}" width="1200" height="800" class="mt-8 w-full rounded-2xl object-cover">
        <div class="prose-content mt-8">{!! $post->body !!}</div>

        <div class="mt-12 rounded-2xl bg-brand p-8 text-center text-white">
            <h2 class="text-3xl !text-white">Planning your Austria trip?</h2>
            <p class="mt-2 text-white/85">Tell us what you love and get a free, personal quote.</p>
            <x-button variant="light" class="mt-5" @click="$store.modal.show({})">Get a Free Quote</x-button>
        </div>
    </div>
</article>

@if($more->isNotEmpty())
<section class="section bg-sand">
    <div class="container-page">
        <x-section-heading eyebrow="Keep reading" title="More from the journal" />
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">@foreach($more as $p)<x-journal-card :post="$p" />@endforeach</div>
    </div>
</section>
@endif
@endsection

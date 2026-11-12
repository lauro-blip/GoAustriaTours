@extends('layouts.app', ['metaTitle' => 'Page not found', 'noindex' => true])

@section('content')
<section class="section">
    <div class="container-page max-w-2xl text-center">
        <p class="font-display text-8xl text-accent">404</p>
        <h1 class="mt-2 text-5xl">We can't find that page</h1>
        <p class="mt-4 text-muted">It may have moved. Try our tours instead.</p>
        <div class="mt-8 flex justify-center gap-4"><x-button :href="route('home')">Home</x-button><x-button variant="outline" :href="route('packages.index')">Packages</x-button></div>
    </div>
</section>
@endsection

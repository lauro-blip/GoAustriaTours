@extends('layouts.app', ['metaTitle' => $page->meta_title, 'metaDescription' => $page->meta_description, 'noindex' => $page->noindex])

@section('content')
<section class="section">
    <div class="container-page max-w-3xl">
        <h1 class="text-5xl">{{ $page->title }}</h1>
        <div class="prose-content mt-8">{!! $page->body !!}</div>
    </div>
</section>
@endsection

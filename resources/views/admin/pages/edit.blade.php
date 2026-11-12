@extends('admin.layout')
@section('title', 'Edit page')
@section('heading', 'Edit: '.$page->title)

@section('content')
<form method="POST" action="{{ route('admin.pages.update', $page) }}" class="max-w-3xl space-y-5 rounded-xl border border-line bg-white p-6">
    @csrf @method('PUT')
    @php $in = 'w-full rounded-lg border border-line px-3 py-2.5'; @endphp
    <div><label class="mb-1 block text-sm font-medium" for="title">Page title (H1)</label><input id="title" name="title" value="{{ old('title', $page->title) }}" required class="{{ $in }}"></div>
    <div><label class="mb-1 block text-sm font-medium" for="mt">SEO title</label><input id="mt" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="{{ $in }}"></div>
    <div><label class="mb-1 block text-sm font-medium" for="md">Meta description</label><textarea id="md" name="meta_description" rows="2" class="{{ $in }}">{{ old('meta_description', $page->meta_description) }}</textarea></div>

    @if($page->slug === 'home')
        <h2 class="text-2xl">Home page text</h2>
        @foreach($homeSections as $key => $label)
            <div><label class="mb-1 block text-sm font-medium" for="s-{{ $key }}">{{ $label }}</label><textarea id="s-{{ $key }}" name="sections[{{ $key }}]" rows="2" class="{{ $in }}">{{ old('sections.'.$key, $page->section($key)) }}</textarea></div>
        @endforeach
    @elseif($page->slug !== 'thank-you')
        <div><label class="mb-1 block text-sm font-medium">Content</label>
            <input id="body-in" type="hidden" name="body" value="{{ old('body', $page->body) }}"><trix-editor input="body-in" class="trix-content min-h-64 bg-white"></trix-editor></div>
        @if(in_array($page->slug, ['terms', 'privacy']))<p class="text-sm text-amber-800">Starter template only. Have a qualified professional review it before launch. If the wording materially changes, bump the terms version in config/goaustria.php.</p>@endif
    @endif

    <label class="flex items-center gap-3 text-sm"><input type="hidden" name="noindex" value="0"><input type="checkbox" name="noindex" value="1" class="size-5 accent-brand" @checked(old('noindex', $page->noindex))> Hide this page from search engines (noindex)</label>
    <div class="flex gap-3"><x-button type="submit" variant="secondary">Save</x-button><x-button :href="route('admin.pages.index')" variant="outline">Cancel</x-button></div>
</form>
@endsection

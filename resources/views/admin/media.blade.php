@extends('admin.layout')
@section('title', 'Media')
@section('heading', 'Media library')

@section('content')
<form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mb-8 flex max-w-3xl flex-wrap items-end gap-3 rounded-xl border border-line bg-white p-4">
    @csrf
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required aria-label="Image" class="text-sm">
    <input name="alt" required placeholder="Alt text (required)" aria-label="Alt text" class="min-w-48 flex-1 rounded-lg border border-line px-3 py-2">
    <x-button type="submit" variant="secondary">Upload</x-button>
    <p class="w-full text-xs text-muted">JPG, PNG or WebP, max 8 MB. Converted to WebP with 480, 960 and 1600 px sizes.</p>
</form>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @forelse($items as $m)
        <figure class="rounded-xl border border-line bg-white p-3">
            <img src="{{ asset('storage/'.$m->path) }}" alt="{{ $m->alt }}" width="{{ $m->width }}" height="{{ $m->height }}" loading="lazy" class="w-full rounded">
            <figcaption class="mt-2 text-xs text-muted break-all">{{ $m->alt }}<br>storage/{{ $m->path }}</figcaption>
            <form method="POST" action="{{ route('admin.media.destroy', $m) }}" onsubmit="return confirm('Delete this image? Pages using it will break.')">@csrf @method('DELETE')<button class="mt-2 text-sm text-red-800 underline">Delete</button></form>
        </figure>
    @empty
        <p class="text-muted">No media yet.</p>
    @endforelse
</div>
<div class="mt-6">{{ $items->links() }}</div>
@endsection

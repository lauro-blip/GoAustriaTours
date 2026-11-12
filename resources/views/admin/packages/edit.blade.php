@extends('admin.layout')
@section('title', 'Edit package')
@section('heading', 'Edit package')
@section('actions')@if($item->is_published)<x-button :href="route('packages.show', $item)" variant="outline" target="_blank">View on site</x-button>@endif @endsection

@section('content')
<div class="grid gap-8 xl:grid-cols-[2fr_1fr]">
    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.packages.update', $item->id) }}" class="space-y-5 rounded-xl border border-line bg-white p-6">
        @csrf @method('PUT')
        @foreach($fields as $f)@include('admin.partials.field', ['f' => $f, 'item' => $item])@endforeach
        <div class="flex gap-3 pt-2"><x-button type="submit" variant="secondary">Save</x-button><x-button :href="route('admin.packages.index')" variant="outline">Back</x-button></div>
    </form>

    <section class="space-y-4" aria-labelledby="gallery-h">
        <h2 id="gallery-h" class="text-3xl">Gallery</h2>
        <form method="POST" action="{{ route('admin.packages.images.store', $item->id) }}" enctype="multipart/form-data" class="space-y-3 rounded-xl border border-line bg-white p-4">
            @csrf
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required aria-label="Image file" class="text-sm">
            <input type="text" name="alt" required placeholder="Alt text (required)" aria-label="Alt text" class="w-full rounded-lg border border-line px-3 py-2">
            <x-button type="submit" variant="secondary">Upload</x-button>
        </form>
        @foreach($item->images as $img)
            <div class="rounded-xl border border-line bg-white p-3">
                <img src="{{ asset('storage/'.$img->path) }}" alt="{{ $img->alt }}" class="w-full rounded">
                <form method="POST" action="{{ route('admin.packages.images.update', $img) }}" class="mt-2 flex gap-2">@csrf @method('PUT')
                    <input type="text" name="alt" value="{{ $img->alt }}" required aria-label="Alt text" class="min-w-0 flex-1 rounded border border-line px-2 py-1 text-sm"><button class="rounded border border-line px-2 text-sm">Save</button></form>
                <div class="mt-2 flex gap-2">
                    <form method="POST" action="{{ route('admin.packages.images.move', [$img, 'up']) }}">@csrf<button class="rounded border border-line px-2 py-1" aria-label="Move up">&uarr;</button></form>
                    <form method="POST" action="{{ route('admin.packages.images.move', [$img, 'down']) }}">@csrf<button class="rounded border border-line px-2 py-1" aria-label="Move down">&darr;</button></form>
                    <form method="POST" action="{{ route('admin.packages.images.destroy', $img) }}" onsubmit="return confirm('Remove this image?')">@csrf @method('DELETE')<button class="rounded border border-red-300 px-2 py-1 text-red-800">Remove</button></form>
                </div>
            </div>
        @endforeach
    </section>
</div>
@endsection

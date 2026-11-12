@extends('admin.layout')
@section('title', $title)
@section('heading', ($item->exists ? 'Edit ' : 'New ').Str::singular($title))

@section('content')
<form method="POST" enctype="multipart/form-data" class="max-w-3xl space-y-5 rounded-xl border border-line bg-white p-6"
      action="{{ $item->exists ? route($routeName.'.update', $item->id) : route($routeName.'.store') }}">
    @csrf
    @if($item->exists) @method('PUT') @endif
    @foreach($fields as $f)@include('admin.partials.field', ['f' => $f, 'item' => $item])@endforeach
    <div class="flex gap-3 pt-2">
        <x-button type="submit" variant="secondary">Save</x-button>
        <x-button :href="route($routeName.'.index')" variant="outline">Cancel</x-button>
    </div>
</form>
@endsection

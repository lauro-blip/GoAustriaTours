@extends('admin.layout')
@section('title', 'Pages')
@section('heading', 'Pages')

@section('content')
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="w-full text-sm">
        <thead class="bg-sand text-left"><tr><th class="p-3">Page</th><th class="p-3">Slug</th><th class="p-3">Search engines</th><th class="p-3 text-right">Actions</th></tr></thead>
        <tbody>@foreach($pages as $p)
            <tr class="border-t border-line"><td class="p-3">{{ $p->title }}</td><td class="p-3">{{ $p->slug }}</td><td class="p-3">{{ $p->noindex ? 'Hidden (noindex)' : 'Indexed' }}</td>
                <td class="p-3 text-right"><a class="rounded bg-brand px-3 py-1 text-white" href="{{ route('admin.pages.edit', $p) }}">Edit</a></td></tr>
        @endforeach</tbody>
    </table>
</div>
<p class="mt-4 text-sm text-muted">Testimonials and home page FAQs have their own menu items.</p>
@endsection

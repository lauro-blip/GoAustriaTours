@extends('admin.layout')
@section('title', $title)
@section('heading', $title)
@section('actions')<x-button :href="route($routeName.'.create')" variant="secondary">Add new</x-button>
<div class="space-y-3 md:hidden">
    @forelse($items as $item)
        <article class="rounded-xl border border-line bg-white p-4 shadow-sm">
            @foreach($columns as $label => $attr)
                @php($v = $item->{$attr})
                @php($shown = (is_bool($v) || in_array($attr, ['is_active', 'is_published', 'is_featured', 'is_placeholder'], true)) ? ($v ? 'Yes' : 'No') : $v)
                @if($loop->first)
                    <h2 class="text-xl font-display text-brand">{{ $shown }}</h2>
                @elseif(filled($shown))
                    <p class="mt-1 text-sm"><span class="text-muted">{{ $label }}:</span> {{ \Illuminate\Support\Str::limit((string) $shown, 90) }}</p>
                @endif
            @endforeach
            <div class="mt-4 border-t border-line pt-3">@include('admin.partials.row-actions', ['item' => $item, 'routeName' => $routeName, 'ordered' => $ordered, 'extraActions' => $extraActions])</div>
        </article>
    @empty
        <p class="rounded-xl border border-line bg-white p-6 text-center text-muted">Nothing here yet.</p>
    @endforelse
</div>
@endsection

@section('content')
<div class="hidden overflow-x-auto rounded-xl border border-line bg-white md:block">
    <table class="w-full text-sm">
        <thead class="bg-sand text-left"><tr>
            @foreach($columns as $label => $attr)<th class="p-3">{{ $label }}</th>@endforeach
            <th class="p-3 text-right">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($items as $item)
            <tr class="border-t border-line">
                @foreach($columns as $label => $attr)
                    <td class="p-3 max-w-xs truncate">
                        @php($v = $item->{$attr})
                        @if(is_bool($v) || in_array($attr, ['is_active','is_published','is_featured','is_placeholder'], true)) {{ $v ? 'Yes' : 'No' }} @else {{ $v }} @endif
                    </td>
                @endforeach
                <td class="p-3">@include('admin.partials.row-actions', ['item' => $item, 'routeName' => $routeName, 'ordered' => $ordered, 'extraActions' => $extraActions])</td>
            </tr>
        @empty
            <tr><td colspan="{{ count($columns) + 1 }}" class="p-6 text-center text-muted">Nothing here yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

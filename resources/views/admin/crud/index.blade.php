@extends('admin.layout')
@section('title', $title)
@section('heading', $title)
@section('actions')<x-button :href="route($routeName.'.create')" variant="secondary">Add new</x-button>@endsection

@section('content')
<div class="overflow-x-auto rounded-xl border border-line bg-white">
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
                <td class="p-3">
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        @if($ordered)
                            <form method="POST" action="{{ route($routeName.'.move', [$item->id, 'up']) }}">@csrf<button class="rounded border border-line px-2 py-1" aria-label="Move up" title="Move up">&uarr;</button></form>
                            <form method="POST" action="{{ route($routeName.'.move', [$item->id, 'down']) }}">@csrf<button class="rounded border border-line px-2 py-1" aria-label="Move down" title="Move down">&darr;</button></form>
                        @endif
                        @foreach($extraActions as $a)<a class="rounded border border-line px-3 py-1" href="{{ route($a['route'], $item->id) }}">{{ $a['label'] }}</a>@endforeach
                        @if($routeName === 'admin.packages')
                            <form method="POST" action="{{ route('admin.packages.toggle', [$item->id, 'is_published']) }}">@csrf<button class="rounded border border-line px-3 py-1">{{ $item->is_published ? 'Unpublish' : 'Publish' }}</button></form>
                            <form method="POST" action="{{ route('admin.packages.toggle', [$item->id, 'is_featured']) }}">@csrf<button class="rounded border border-line px-3 py-1">{{ $item->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
                            <form method="POST" action="{{ route('admin.packages.duplicate', $item->id) }}">@csrf<button class="rounded border border-line px-3 py-1">Duplicate</button></form>
                        @endif
                        <a class="rounded bg-brand px-3 py-1 text-white" href="{{ route($routeName.'.edit', $item->id) }}">Edit</a>
                        <form method="POST" action="{{ route($routeName.'.destroy', $item->id) }}" onsubmit="return confirm('Delete this item?')">@csrf @method('DELETE')<button class="rounded border border-red-300 px-3 py-1 text-red-800">Delete</button></form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="{{ count($columns) + 1 }}" class="p-6 text-center text-muted">Nothing here yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

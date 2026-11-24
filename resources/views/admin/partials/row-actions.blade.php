{{-- Row buttons for the CRUD lists. Vars: $item, $routeName, $ordered, $extraActions. --}}
<div class="flex flex-wrap items-center gap-2 md:justify-end">
    @if($ordered)
        <form method="POST" action="{{ route($routeName.'.move', [$item->id, 'up']) }}">@csrf<button class="rounded border border-line px-3 py-2 md:px-2 md:py-1" aria-label="Move up" title="Move up">&uarr;</button></form>
        <form method="POST" action="{{ route($routeName.'.move', [$item->id, 'down']) }}">@csrf<button class="rounded border border-line px-3 py-2 md:px-2 md:py-1" aria-label="Move down" title="Move down">&darr;</button></form>
    @endif
    @foreach($extraActions as $a)<a class="rounded border border-line px-3 py-2 md:py-1" href="{{ route($a['route'], $item->id) }}">{{ $a['label'] }}</a>@endforeach
    @if($routeName === 'admin.packages')
        <form method="POST" action="{{ route('admin.packages.toggle', [$item->id, 'is_published']) }}">@csrf<button class="rounded border border-line px-3 py-2 md:py-1">{{ $item->is_published ? 'Unpublish' : 'Publish' }}</button></form>
        <form method="POST" action="{{ route('admin.packages.toggle', [$item->id, 'is_featured']) }}">@csrf<button class="rounded border border-line px-3 py-2 md:py-1">{{ $item->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        <form method="POST" action="{{ route('admin.packages.duplicate', $item->id) }}">@csrf<button class="rounded border border-line px-3 py-2 md:py-1">Duplicate</button></form>
    @endif
    <a class="rounded bg-brand px-4 py-2 text-white md:px-3 md:py-1" href="{{ route($routeName.'.edit', $item->id) }}">Edit</a>
    <form method="POST" action="{{ route($routeName.'.destroy', $item->id) }}" onsubmit="return confirm('Delete this item?')">@csrf @method('DELETE')<button class="rounded border border-red-300 px-3 py-2 text-red-800 md:py-1">Delete</button></form>
</div>

@extends('admin.layout')
@section('title', 'Quiz options')
@section('heading', 'Options: '.$step->question)
@section('actions')<x-button :href="route('admin.quiz.index')" variant="outline">Back to steps</x-button>@endsection

@section('content')
<p class="mb-4 max-w-2xl text-sm text-muted">Label is what visitors see. Value is what is stored and sent to the CRM. Icons show on tile questions only.</p>
<div class="space-y-3">
    @foreach($step->options as $o)
        <div class="flex flex-wrap items-center gap-2 rounded-xl border border-line bg-white p-3">
            <form method="POST" action="{{ route('admin.quiz.options.save', [$step->id, $o->id]) }}" class="flex flex-1 flex-wrap items-center gap-2">
                @csrf
                <input name="label" value="{{ $o->label }}" required aria-label="Label" class="min-w-40 flex-1 rounded border border-line px-2 py-1.5">
                <input name="value" value="{{ $o->value }}" required aria-label="Value" class="w-32 rounded border border-line px-2 py-1.5">
                <select name="icon" aria-label="Icon" class="rounded border border-line px-2 py-1.5"><option value="">No icon</option>@foreach($icons as $i)<option @selected($o->icon === $i)>{{ $i }}</option>@endforeach</select>
                <label class="text-sm"><input type="checkbox" name="is_active" value="1" @checked($o->is_active)> Active</label>
                <button class="rounded bg-brand px-3 py-1.5 text-white">Save</button>
            </form>
            <form method="POST" action="{{ route('admin.quiz.options.move', [$step->id, $o->id, 'up']) }}">@csrf<button class="rounded border border-line px-2 py-1" aria-label="Move up">&uarr;</button></form>
            <form method="POST" action="{{ route('admin.quiz.options.move', [$step->id, $o->id, 'down']) }}">@csrf<button class="rounded border border-line px-2 py-1" aria-label="Move down">&darr;</button></form>
            <form method="POST" action="{{ route('admin.quiz.options.delete', [$step->id, $o->id]) }}" onsubmit="return confirm('Remove this option?')">@csrf @method('DELETE')<button class="rounded border border-red-300 px-2 py-1 text-red-800">Delete</button></form>
        </div>
    @endforeach
</div>

<h2 class="mt-8 mb-2 text-2xl">Add option</h2>
<form method="POST" action="{{ route('admin.quiz.options.save', $step->id) }}" class="flex flex-wrap items-center gap-2 rounded-xl border border-line bg-white p-3">
    @csrf
    <input name="label" required placeholder="Label" aria-label="Label" class="min-w-40 flex-1 rounded border border-line px-2 py-1.5">
    <input name="value" required placeholder="Value" aria-label="Value" class="w-32 rounded border border-line px-2 py-1.5">
    <select name="icon" aria-label="Icon" class="rounded border border-line px-2 py-1.5"><option value="">No icon</option>@foreach($icons as $i)<option>{{ $i }}</option>@endforeach</select>
    <label class="text-sm"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    <button class="rounded bg-brand px-3 py-1.5 text-white">Add</button>
</form>
@endsection

{{-- Renders one field from a CrudController field definition. --}}
@php
    $name = $f['name'];
    $type = $f['type'];
    $id = 'fld-'.$name;
    $raw = old($name, $item->{$name} ?? null);
    $err = $errors->first($name);
    $cls = 'w-full rounded-lg border bg-white px-3 py-2.5 text-base '.($err ? 'border-red-700' : 'border-line');
@endphp
<div>
    @if($type === 'checkbox')
        <label class="flex items-center gap-3 text-sm"><input type="hidden" name="{{ $name }}" value="0"><input type="checkbox" name="{{ $name }}" value="1" class="size-5 accent-brand" @checked(old($name, $item->exists ? $item->{$name} : ($f['default'] ?? ($name === 'is_active'))))> {{ $f['label'] }}</label>
    @else
        <label for="{{ $id }}" class="mb-1 block text-sm font-medium">{{ $f['label'] }}</label>
        @switch($type)
            @case('textarea')
                <textarea id="{{ $id }}" name="{{ $name }}" rows="4" class="{{ $cls }}">{{ $raw }}</textarea>
                @break
            @case('richtext')
                <input id="{{ $id }}-in" type="hidden" name="{{ $name }}" value="{{ $raw }}">
                <trix-editor input="{{ $id }}-in" class="trix-content min-h-48 bg-white"></trix-editor>
                @break
            @case('number')
                <input id="{{ $id }}" type="number" name="{{ $name }}" step="{{ $f['step'] ?? 1 }}" value="{{ $raw }}" class="{{ $cls }}">
                @break
            @case('password')
                <input id="{{ $id }}" type="password" name="{{ $name }}" autocomplete="new-password" class="{{ $cls }}">
                @break
            @case('select')
                <select id="{{ $id }}" name="{{ $name }}" class="{{ $cls }}">
                    @if(! empty($f['blank']))<option value="">None</option>@endif
                    @foreach($f['options'] as $val => $label)<option value="{{ $val }}" @selected((string) $raw === (string) $val)>{{ $label }}</option>@endforeach
                </select>
                @break
            @case('lines')
                <textarea id="{{ $id }}" name="{{ $name }}" rows="5" class="{{ $cls }}">{{ is_array($raw) ? implode("\n", $raw) : $raw }}</textarea>
                @break
            @case('image')
                @if($item->{$name} ?? null)
                    <div class="mb-2 flex items-center gap-3"><img src="{{ asset('storage/'.$item->{$name}) }}" alt="" class="h-20 rounded border border-line"><label class="text-sm"><input type="checkbox" name="{{ $name }}_remove" value="1"> Remove</label></div>
                @endif
                <input id="{{ $id }}" type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp" class="text-sm">
                <p class="mt-1 text-xs text-muted">JPG, PNG or WebP up to 8 MB. Converted to WebP and resized automatically.</p>
                @break
            @case('repeater')
                @php $rows = old($name, $item->{$name} ?? []) ?: []; @endphp
                <div x-data="{ rows: @js(array_values($rows)) }" class="space-y-3">
                    <template x-for="(row, i) in rows" :key="i">
                        <div class="rounded-lg border border-line p-3 space-y-2">
                            @foreach($f['sub'] as $s)
                                @if(! empty($s['area']))
                                    <textarea rows="2" :name="`{{ $name }}[${i}][{{ $s['name'] }}]`" x-model="row.{{ $s['name'] }}" placeholder="{{ $s['label'] }}" aria-label="{{ $s['label'] }}" class="{{ $cls }}"></textarea>
                                @else
                                    <input type="text" :name="`{{ $name }}[${i}][{{ $s['name'] }}]`" x-model="row.{{ $s['name'] }}" placeholder="{{ $s['label'] }}" aria-label="{{ $s['label'] }}" class="{{ $cls }}">
                                @endif
                            @endforeach
                            <button type="button" class="text-sm text-red-800 underline" @click="rows.splice(i, 1)">Remove</button>
                        </div>
                    </template>
                    <button type="button" class="rounded border border-line px-3 py-1.5 text-sm" @click="rows.push({ @foreach($f['sub'] as $s){{ $s['name'] }}: '',@endforeach })">+ Add</button>
                </div>
                @break
            @default
                <input id="{{ $id }}" type="text" name="{{ $name }}" value="{{ $raw }}" class="{{ $cls }}">
        @endswitch
        @if($err)<p class="mt-1 text-sm text-red-700" role="alert">{{ $err }}</p>@endif
    @endif
</div>

{{-- One block field in the page builder (Alpine). Vars: $obj (JS expression of the object holding the value), $k (JS expression of the key), $f (JS expression of the field definition). --}}
@php $v = $obj.'['.$k.']'; $in = 'w-full rounded-lg border border-line bg-white px-3 py-2.5'; @endphp
<div>
    <template x-if="{{ $f }}.kind !== 'items'"><label class="mb-1 block text-sm font-medium" x-text="{{ $f }}.label"></label></template>

    <template x-if="{{ $f }}.kind === 'text' || {{ $f }}.kind === 'link'">
        <input type="text" x-model="{{ $v }}" :maxlength="{{ $f }}.max || 300" :placeholder="{{ $f }}.kind === 'link' ? '/destinations or https://...' : ''" class="{{ $in }}">
    </template>
    <template x-if="{{ $f }}.kind === 'textarea'">
        <textarea x-model="{{ $v }}" :maxlength="{{ $f }}.max || 2000" rows="3" class="{{ $in }}"></textarea>
    </template>
    <template x-if="{{ $f }}.kind === 'number'">
        <input type="number" x-model.number="{{ $v }}" :min="{{ $f }}.min" :max="{{ $f }}.max" class="{{ $in }} max-w-32">
    </template>
    <template x-if="{{ $f }}.kind === 'select'">
        <select x-model="{{ $v }}" class="{{ $in }} max-w-md">
            <template x-for="[ov, ol] in Object.entries({{ $f }}.options)" :key="ov"><option :value="ov" x-text="ol" :selected="ov === {{ $v }}"></option></template>
        </select>
    </template>
    <template x-if="{{ $f }}.kind === 'rich'">
        <trix-editor class="trix-content min-h-40 rounded-lg border border-line bg-white" x-on:trix-initialize="$event.target.editor.loadHTML({{ $v }} || '')" x-on:trix-change="{{ $v }} = $event.target.value"></trix-editor>
    </template>
    <template x-if="{{ $f }}.kind === 'image'">
        <div class="flex flex-wrap items-start gap-4">
            <button type="button" class="grid aspect-[4/3] w-40 shrink-0 place-items-center overflow-hidden rounded-lg border border-dashed border-secondary bg-sand/50 text-xs text-muted" @click="openPicker({{ $v }})">
                <img x-show="{{ $v }}.ref" :src="thumb({{ $v }}.ref)" alt="" class="size-full object-cover">
                <span x-show="! {{ $v }}.ref">Choose a photo</span>
            </button>
            <div class="min-w-56 flex-1 space-y-2">
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="rounded border border-line px-3 py-1.5 text-sm" @click="openPicker({{ $v }})" x-text="{{ $v }}.ref ? 'Change photo' : 'Choose photo'"></button>
                    <button type="button" class="rounded border border-line px-3 py-1.5 text-sm" x-show="{{ $v }}.ref" @click="clearImage({{ $v }})">Remove</button>
                </div>
                <input type="text" x-show="{{ $v }}.ref && ! {{ $f }}.decorative" x-model="{{ $v }}.alt" maxlength="190" placeholder="Describe the photo (alt text)" class="{{ $in }} text-sm">
                <p x-show="{{ $v }}.ref && ! {{ $v }}.alt && ! {{ $f }}.decorative" class="text-xs text-amber-800">Add a short description: it helps Google and screen reader users.</p>
            </div>
        </div>
    </template>
    <template x-if="{{ $f }}.help"><p class="mt-1 text-xs text-muted" x-text="{{ $f }}.help"></p></template>
</div>

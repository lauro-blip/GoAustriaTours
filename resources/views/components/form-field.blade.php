{{--
    Labelled form field. `active` scopes errors/old input to one form when several forms
    share the same field names on a page (see enquiry-form).
--}}
@props(['name', 'label', 'type' => 'text', 'required' => false, 'value' => null, 'id' => null, 'autocomplete' => null, 'active' => true, 'placeholder' => null])
@php
    $fid = ($id ?? 'f').'-'.$name;
    $msg = $active ? $errors->first($name) : null;
    $val = $active ? old($name, $value) : $value;
    $inputClass = 'w-full rounded-lg border bg-white px-3 py-2.5 text-base focus:border-brand '.($msg ? 'border-red-700' : 'border-line');
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $fid }}" class="block text-sm font-medium text-ink mb-1">{{ $label }}@if($required) <span class="text-red-700" aria-hidden="true">*</span>@endif</label>
    @if($type === 'textarea')
        <textarea id="{{ $fid }}" name="{{ $name }}" rows="3" @required($required) class="{{ $inputClass }}" @if($msg) aria-invalid="true" aria-describedby="{{ $fid }}-err" @endif>{{ $val }}</textarea>
    @elseif($type === 'select')
        <select id="{{ $fid }}" name="{{ $name }}" @required($required) class="{{ $inputClass }}" @if($msg) aria-invalid="true" aria-describedby="{{ $fid }}-err" @endif>{{ $slot }}</select>
    @else
        <input id="{{ $fid }}" type="{{ $type }}" name="{{ $name }}" value="{{ $val }}" @required($required) @if($placeholder) placeholder="{{ $placeholder }}" @endif @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif class="{{ $inputClass }}" @if($msg) aria-invalid="true" aria-describedby="{{ $fid }}-err" @endif>
    @endif
    @if($msg)<p id="{{ $fid }}-err" class="mt-1 text-sm text-red-700" role="alert">{{ $msg }}</p>@endif
</div>

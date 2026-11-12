@extends('admin.layout')
@section('title', 'Settings')
@section('heading', 'Site settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="max-w-3xl space-y-8">
    @csrf @method('PUT')
    @foreach($groups as $group => $keys)
        <fieldset class="space-y-4 rounded-xl border border-line bg-white p-6">
            <legend class="px-2 text-2xl font-display">{{ $group }}</legend>
            @foreach($keys as $key => [$label, $type])
                <div>
                    <label for="s-{{ $key }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
                    @if($type === 'textarea')
                        <textarea id="s-{{ $key }}" name="{{ $key }}" rows="3" class="w-full rounded-lg border border-line px-3 py-2.5">{{ old($key, $values[$key] ?? '') }}</textarea>
                    @elseif($type === 'image')
                        @if(! empty($values[$key]))<div class="mb-2 flex items-center gap-3"><img src="{{ asset('storage/'.$values[$key]) }}" alt="" class="h-16 rounded border border-line"><label class="text-sm"><input type="checkbox" name="{{ $key }}_remove" value="1"> Remove</label></div>@endif
                        <input id="s-{{ $key }}" type="file" name="{{ $key }}" accept="image/jpeg,image/png,image/webp" class="text-sm">
                    @elseif($type === 'password')
                        <input id="s-{{ $key }}" type="password" name="{{ $key }}" autocomplete="new-password" placeholder="{{ ! empty($values[$key]) ? 'Saved (leave blank to keep)' : '' }}" class="w-full rounded-lg border border-line px-3 py-2.5">
                    @else
                        <input id="s-{{ $key }}" type="text" name="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" class="w-full rounded-lg border border-line px-3 py-2.5">
                    @endif
                </div>
            @endforeach
        </fieldset>
    @endforeach

    <fieldset class="rounded-xl border border-line bg-white p-6">
        <legend class="px-2 text-2xl font-display">GoHighLevel</legend>
        <p class="text-sm">Sync status: <strong>{{ $ghlOn ? 'ON' : 'OFF' }}</strong>. The token and location ID are set in the server <code>.env</code> file (GHL_API_TOKEN, GHL_LOCATION_ID) and cannot be edited here.</p>
    </fieldset>
    <x-button type="submit" variant="secondary">Save settings</x-button>
</form>
@endsection

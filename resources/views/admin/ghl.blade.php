@extends('admin.layout')
@section('title', 'GoHighLevel')
@section('heading', 'GoHighLevel connection')

@section('content')
<div class="max-w-3xl space-y-8">
    <div class="rounded-xl border border-line bg-white p-6">
        <p class="text-sm">Sync status: <strong class="{{ $on ? 'text-green-700' : 'text-red-700' }}">{{ $on ? 'ON' : 'OFF' }}</strong>
            @if(! $on) &mdash; enquiries are saved here but not sent to GoHighLevel until both values below are set.@endif</p>
        @error('ghl')<p class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-800" role="alert">{{ $message }}</p>@enderror
    </div>

    <form method="POST" action="{{ route('admin.ghl.update') }}" autocomplete="off" class="space-y-5 rounded-xl border border-line bg-white p-6">
        @csrf @method('PUT')
        <h2 class="text-2xl font-display">Keys</h2>
        <p class="text-sm text-muted">In GoHighLevel: Settings &gt; Private Integrations &gt; create an integration with the Contacts and Custom Fields scopes, then copy its token. The Location ID is under Settings &gt; Business Profile (or in the sub-account URL).</p>

        <div>
            <label for="ghl_location_id" class="mb-1 block text-sm font-medium">Location ID</label>
            <input id="ghl_location_id" type="text" name="ghl_location_id" value="{{ old('ghl_location_id', $locationId) }}" placeholder="{{ $envLocation ? 'Using the value from .env' : '' }}" class="w-full rounded-lg border border-line px-3 py-2.5 font-mono text-sm" spellcheck="false">
            @error('ghl_location_id')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="ghl_api_token" class="mb-1 block text-sm font-medium">Private Integration Token (PIT)</label>
            <input id="ghl_api_token" type="password" name="ghl_api_token" autocomplete="new-password" spellcheck="false"
                placeholder="{{ $tokenHint ? 'Saved ('.$tokenHint.'). Leave blank to keep it.' : ($envToken ? 'Using the value from .env' : 'pit-...') }}" class="w-full rounded-lg border border-line px-3 py-2.5 font-mono text-sm">
            <p class="mt-1 text-xs text-muted">Stored encrypted and never shown again. Paste a new token to replace it.</p>
            @error('ghl_api_token')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        @if($hasSaved)
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="clear" value="1"> Remove the saved values (fall back to the server .env file, or switch sync off)</label>
        @endif

        <x-button type="submit" variant="secondary">Save</x-button>
    </form>

    <form method="POST" action="{{ route('admin.ghl.test') }}" class="rounded-xl border border-line bg-white p-6">
        @csrf
        <h2 class="text-2xl font-display">Test connection</h2>
        <p class="mt-2 text-sm text-muted">Checks the saved token and Location ID by reading the contact custom fields. It does not create or change anything in GoHighLevel. Save first.</p>
        <x-button type="submit" variant="secondary" class="mt-4">Test connection</x-button>
    </form>
</div>
@endsection

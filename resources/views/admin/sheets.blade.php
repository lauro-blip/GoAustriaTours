@extends('admin.layout')
@section('title', 'Google Sheet')
@section('heading', 'Google Sheet log')

@section('content')
<div class="max-w-3xl space-y-8">
    <div class="rounded-xl border border-line bg-white p-6">
        <p class="text-sm">Sheet log: <strong class="{{ $on ? 'text-green-700' : 'text-red-700' }}">{{ $on ? 'ON' : 'OFF' }}</strong>
            @if(! $on) &mdash; enquiries are saved here but not added to the sheet until both values below are set.@endif</p>
        <p class="mt-2 text-sm text-muted">Every form submission is added as a new row, with one tab per form: {{ implode(', ', $tabs) }}. All stored fields are included (contact details, answers, consent record and all UTM / click-ID values).</p>
        @error('sheets')<p class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-800" role="alert">{{ $message }}</p>@enderror
    </div>

    <form method="POST" action="{{ route('admin.sheets.update') }}" autocomplete="off" class="space-y-5 rounded-xl border border-line bg-white p-6">
        @csrf @method('PUT')
        <h2 class="text-2xl font-display">Connection</h2>
        <p class="text-sm text-muted">Follow the steps in <code>docs/reference/GOOGLE_SHEET.md</code> (about 3 minutes): paste the script into the sheet, deploy it as a web app, then paste its address and your chosen secret here and into the script.</p>

        <div>
            <label for="sheets_webhook_url" class="mb-1 block text-sm font-medium">Web app URL</label>
            <input id="sheets_webhook_url" type="url" name="sheets_webhook_url" value="{{ old('sheets_webhook_url', $url) }}" placeholder="{{ $envOn ? 'Using the value from .env' : 'https://script.google.com/macros/s/.../exec' }}" class="w-full rounded-lg border border-line px-3 py-2.5 font-mono text-sm" spellcheck="false">
            @error('sheets_webhook_url')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="sheets_secret" class="mb-1 block text-sm font-medium">Secret</label>
            <input id="sheets_secret" type="password" name="sheets_secret" autocomplete="new-password" spellcheck="false" placeholder="{{ $secretHint ? 'Saved ('.$secretHint.'). Leave blank to keep it.' : 'The same secret you put in the script' }}" class="w-full rounded-lg border border-line px-3 py-2.5 font-mono text-sm">
            <p class="mt-1 text-xs text-muted">Stored encrypted and never shown again.</p>
            @error('sheets_secret')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>

        @if($hasSaved)
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="clear" value="1"> Remove the saved values (switch the sheet log off)</label>
        @endif

        <x-button type="submit" variant="secondary">Save</x-button>
    </form>

    <form method="POST" action="{{ route('admin.sheets.test') }}" class="rounded-xl border border-line bg-white p-6">
        @csrf
        <h2 class="text-2xl font-display">Send a test row</h2>
        <p class="mt-2 text-sm text-muted">Adds one labelled row to a "Connection test" tab in your sheet. Save first.</p>
        <x-button type="submit" variant="secondary" class="mt-4">Send a test row</x-button>
    </form>
</div>
@endsection

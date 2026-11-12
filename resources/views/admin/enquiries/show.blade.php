@extends('admin.layout')
@section('title', 'Enquiry #'.$enquiry->id)
@section('heading', 'Enquiry #'.$enquiry->id.': '.$enquiry->full_name)
@section('actions')<x-button :href="route('admin.enquiries.index')" variant="outline">Back to inbox</x-button>@endsection

@section('content')
@php
    $row = fn ($k, $v) => '<div class="flex gap-3 border-b border-line py-2 text-sm"><dt class="w-40 shrink-0 font-medium">'.e($k).'</dt><dd class="min-w-0 break-words">'.e($v ?: '-').'</dd></div>';
@endphp
<div class="grid gap-6 lg:grid-cols-2">
    <section class="rounded-xl border border-line bg-white p-5">
        <h2 class="mb-2 text-2xl">Contact and trip</h2>
        <dl>
            {!! $row('Email', $enquiry->email) !!}{!! $row('Phone (E.164)', $enquiry->phone_e164) !!}{!! $row('Form', $enquiry->form_name) !!}
            {!! $row('Package', $enquiry->package_name) !!}{!! $row('Travel date', $enquiry->travel_date) !!}{!! $row('Second quote', $enquiry->second_quote) !!}
            {!! $row('Special request', $enquiry->special_request) !!}{!! $row('Received', $enquiry->created_at->format('d M Y H:i')) !!}
        </dl>
        <h3 class="mt-5 mb-2 text-xl">Quiz answers</h3>
        <dl>@forelse($enquiry->quiz_answers ?? [] as $k => $v){!! $row($k, $v) !!}@empty<p class="text-sm text-muted">None.</p>@endforelse</dl>
    </section>

    <section class="rounded-xl border border-line bg-white p-5">
        <h2 class="mb-2 text-2xl">Status and notes</h2>
        <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}" class="space-y-3">
            @csrf @method('PUT')
            <select name="status" aria-label="Status" class="w-full rounded-lg border border-line px-3 py-2">@foreach(\App\Models\Enquiry::STATUSES as $s)<option value="{{ $s }}" @selected($enquiry->status === $s)>{{ ucfirst($s) }}</option>@endforeach</select>
            <textarea name="notes" rows="5" aria-label="Internal notes" placeholder="Internal notes" class="w-full rounded-lg border border-line px-3 py-2">{{ old('notes', $enquiry->notes) }}</textarea>
            <x-button type="submit" variant="secondary">Save</x-button>
        </form>
        <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}" class="mt-4" onsubmit="return confirm('Delete this enquiry permanently?')">@csrf @method('DELETE')<button class="text-sm text-red-800 underline">Delete enquiry</button></form>
    </section>

    <section class="rounded-xl border border-line bg-white p-5">
        <h2 class="mb-2 text-2xl">Consent evidence</h2>
        <dl>
            {!! $row('Consent given', $enquiry->consent_given ? 'Yes' : 'No') !!}{!! $row('Consented at', $enquiry->consented_at?->toDateTimeString().' UTC') !!}
            {!! $row('Terms version', $enquiry->terms_version) !!}{!! $row('IP address', $enquiry->ip_address) !!}{!! $row('User agent', $enquiry->user_agent) !!}{!! $row('Page URL', $enquiry->page_url) !!}
        </dl>
        <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-muted">Consent text shown</p>
        <p class="mt-1 text-sm">{{ $enquiry->consent_text }}</p>
    </section>

    <section class="rounded-xl border border-line bg-white p-5">
        <h2 class="mb-2 text-2xl">Tracking</h2>
        @foreach(['First touch' => $enquiry->tracking_first, 'Last touch' => $enquiry->tracking_last] as $label => $t)
            <h3 class="mt-3 text-lg">{{ $label }}</h3>
            <dl>@foreach($t ?? [] as $k => $v){!! $row($k, $v) !!}@endforeach</dl>
        @endforeach
        <h3 class="mt-5 text-xl">GoHighLevel</h3>
        <dl>{!! $row('Sync status', $enquiry->ghl_status) !!}{!! $row('Contact ID', $enquiry->ghl_contact_id) !!}{!! $row('Last error', $enquiry->ghl_error) !!}{!! $row('Synced at', $enquiry->ghl_synced_at) !!}</dl>
        <form method="POST" action="{{ route('admin.enquiries.ghl-retry', $enquiry) }}" class="mt-3">@csrf<x-button type="submit" variant="outline">Retry GoHighLevel sync</x-button></form>
    </section>
</div>
@endsection

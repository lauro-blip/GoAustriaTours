@extends('admin.layout')
@section('title', 'Enquiries')
@section('heading', 'Enquiries')
@section('actions')<x-button :href="route('admin.enquiries.export', request()->query())" variant="outline">Export CSV</x-button>@endsection

@section('content')
<form method="GET" class="mb-5 grid gap-3 rounded-xl border border-line bg-white p-4 sm:grid-cols-2 lg:grid-cols-6">
    <input name="q" value="{{ request('q') }}" placeholder="Search name, email, phone" aria-label="Search" class="rounded-lg border border-line px-3 py-2 lg:col-span-2">
    <select name="status" aria-label="Status" class="rounded-lg border border-line px-3 py-2"><option value="">All statuses</option>@foreach(\App\Models\Enquiry::STATUSES as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach</select>
    <input name="source" value="{{ request('source') }}" placeholder="utm_source" aria-label="Source" class="rounded-lg border border-line px-3 py-2">
    <input type="date" name="from" value="{{ request('from') }}" aria-label="From date" class="rounded-lg border border-line px-3 py-2">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="To date" class="rounded-lg border border-line px-3 py-2">
    <x-button type="submit" variant="secondary">Filter</x-button>
</form>

<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="w-full text-sm">
        <thead class="bg-sand text-left"><tr><th class="p-3">Date</th><th class="p-3">Name</th><th class="p-3">Contact</th><th class="p-3">Form / package</th><th class="p-3">Source</th><th class="p-3">Status</th><th class="p-3">CRM</th></tr></thead>
        <tbody>
        @forelse($enquiries as $e)
            <tr class="border-t border-line">
                <td class="p-3 whitespace-nowrap">{{ $e->created_at->format('d M Y H:i') }}</td>
                <td class="p-3"><a class="underline text-brand" href="{{ route('admin.enquiries.show', $e) }}">{{ $e->full_name }}</a></td>
                <td class="p-3">{{ $e->email }}<br><span class="text-muted">{{ $e->phone_e164 }}</span></td>
                <td class="p-3">{{ $e->form_name }}<br><span class="text-muted">{{ $e->package_name }}</span></td>
                <td class="p-3">{{ $e->tracking_last['utm_source'] ?? $e->tracking_first['utm_source'] ?? '-' }}</td>
                <td class="p-3 capitalize">{{ $e->status }}</td>
                <td class="p-3">{{ $e->ghl_status }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="p-6 text-center text-muted">No enquiries match.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $enquiries->links() }}</div>
@endsection

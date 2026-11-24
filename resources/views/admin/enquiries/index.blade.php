@extends('admin.layout')
@section('title', 'Enquiries')
@section('heading', 'Enquiries')
@section('actions')<x-button :href="route('admin.enquiries.export', request()->query())" variant="outline">Export CSV</x-button>@endsection

@section('content')
<form method="GET" class="mb-5 grid gap-3 rounded-xl border border-line bg-white p-4 sm:grid-cols-2 xl:grid-cols-[2fr_1fr_1fr_1fr_1fr_auto]">
    <input name="q" value="{{ request('q') }}" placeholder="Search name, email, phone" aria-label="Search" class="rounded-lg border border-line px-3 py-2">
    <select name="status" aria-label="Status" class="rounded-lg border border-line px-3 py-2"><option value="">All statuses</option>@foreach(\App\Models\Enquiry::STATUSES as $s)<option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>@endforeach</select>
    <input name="source" value="{{ request('source') }}" placeholder="utm_source" aria-label="Source" class="rounded-lg border border-line px-3 py-2">
    <input type="date" name="from" value="{{ request('from') }}" aria-label="From date" class="rounded-lg border border-line px-3 py-2">
    <input type="date" name="to" value="{{ request('to') }}" aria-label="To date" class="rounded-lg border border-line px-3 py-2">
    <x-button type="submit" variant="secondary" class="!py-2">Filter</x-button>
</form>
@if(request('sync') === 'failed' || request('stale') === '1')
<p class="mb-4 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-950">Showing only {{ request('sync') === 'failed' ? 'enquiries that failed to sync' : 'new enquiries waiting more than 24 hours' }}. <a class="underline" href="{{ route('admin.enquiries.index') }}">Show all</a></p>
@endif

<div class="hidden overflow-x-auto rounded-xl border border-line bg-white md:block">
    <table class="w-full text-sm">
        <thead class="bg-sand text-left"><tr><th class="p-3">Date</th><th class="p-3">Name</th><th class="p-3">Contact</th><th class="p-3">Form / package</th><th class="p-3">Source</th><th class="p-3">Status</th><th class="p-3">Sync</th></tr></thead>
        <tbody>
        @forelse($enquiries as $e)
            <tr class="border-t border-line">
                <td class="p-3 whitespace-nowrap">{{ $e->created_at->format('d M Y H:i') }}</td>
                <td class="p-3"><a class="underline text-brand" href="{{ route('admin.enquiries.show', $e) }}">{{ $e->full_name }}</a></td>
                <td class="p-3">{{ $e->email }}<br><span class="text-muted">{{ $e->phone_e164 }}</span></td>
                <td class="p-3">{{ $e->form_name }}<br><span class="text-muted">{{ $e->package_name }}</span></td>
                <td class="p-3">{{ $e->tracking_last['utm_source'] ?? $e->tracking_first['utm_source'] ?? '-' }}</td>
                <td class="p-3"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs capitalize {{ ['new' => 'bg-amber-100 text-amber-900', 'contacted' => 'bg-blue-100 text-blue-900', 'quoted' => 'bg-indigo-100 text-indigo-900', 'won' => 'bg-green-100 text-green-900', 'lost' => 'bg-red-100 text-red-900'][$e->status] ?? 'bg-sand' }}">{{ $e->status }}</span></td>
                <td class="p-3 text-xs">
                    @foreach(['CRM' => $e->ghl_status, 'Sheet' => $e->sheet_status] as $sl => $sv)
                        <span class="mr-1 inline-block rounded-full px-2 py-0.5 {{ ['synced' => 'bg-green-100 text-green-900', 'failed' => 'bg-red-100 text-red-900', 'pending' => 'bg-amber-100 text-amber-900'][$sv] ?? 'bg-sand text-muted' }}" title="{{ $sl }}: {{ $sv }}">{{ $sl }} {{ $sv === 'disabled' ? 'off' : $sv }}</span>
                    @endforeach
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="p-6 text-center text-muted">No enquiries match.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="space-y-3 md:hidden">
    @forelse($enquiries as $e)
        <a href="{{ route('admin.enquiries.show', $e) }}" class="block rounded-xl border border-line bg-white p-4 shadow-sm active:bg-brand-soft">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate font-semibold text-brand">{{ $e->full_name }}</p>
                    <p class="truncate text-sm text-muted">{{ $e->email }}</p>
                </div>
                <span class="flex-none rounded-full px-2.5 py-0.5 text-xs capitalize {{ ['new' => 'bg-amber-100 text-amber-900', 'contacted' => 'bg-blue-100 text-blue-900', 'quoted' => 'bg-indigo-100 text-indigo-900', 'won' => 'bg-green-100 text-green-900', 'lost' => 'bg-red-100 text-red-900'][$e->status] ?? 'bg-sand' }}">{{ $e->status }}</span>
            </div>
            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                <div><dt class="text-xs text-muted">Received</dt><dd>{{ $e->created_at->format('d M, H:i') }}</dd></div>
                <div><dt class="text-xs text-muted">Form</dt><dd>{{ \App\Support\SheetRow::TABS[$e->form_name] ?? $e->form_name }}</dd></div>
                @if($e->phone_e164)<div><dt class="text-xs text-muted">Phone</dt><dd>{{ $e->phone_e164 }}</dd></div>@endif
                <div><dt class="text-xs text-muted">Source</dt><dd>{{ \App\Http\Controllers\Admin\DashboardController::sourceLabel($e) }}</dd></div>
                @if($e->package_name)<div class="col-span-2"><dt class="text-xs text-muted">Package</dt><dd class="truncate">{{ $e->package_name }}</dd></div>@endif
            </dl>
            <p class="mt-3 flex flex-wrap gap-1.5 text-xs">
                @foreach(['CRM' => $e->ghl_status, 'Sheet' => $e->sheet_status] as $sl => $sv)
                    <span class="rounded-full px-2 py-0.5 {{ ['synced' => 'bg-green-100 text-green-900', 'failed' => 'bg-red-100 text-red-900', 'pending' => 'bg-amber-100 text-amber-900'][$sv] ?? 'bg-sand text-muted' }}">{{ $sl }} {{ $sv === 'disabled' ? 'off' : $sv }}</span>
                @endforeach
            </p>
        </a>
    @empty
        <p class="rounded-xl border border-line bg-white p-6 text-center text-muted">No enquiries match.</p>
    @endforelse
</div>
<div class="mt-4">{{ $enquiries->links() }}</div>
@endsection

@extends('admin.layout')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('actions')
    <div class="inline-flex overflow-hidden rounded-full border border-line bg-white text-sm" role="group" aria-label="Period">
        @foreach($ranges as $r)
            <a href="{{ route('admin.dashboard', ['range' => $r]) }}" class="px-4 py-2 {{ $range === $r ? 'bg-brand text-white' : 'text-brand hover:bg-brand-soft' }}" @if($range === $r) aria-current="true" @endif>{{ $r }} days</a>
        @endforeach
    </div>
@endsection

@section('content')
@php
    $delta = function (int $now, int $before) {
        if ($before === 0) { return $now > 0 ? ['+'.$now.' new', 'up'] : [null, null]; }
        $p = round(($now - $before) / $before * 100);
        return [($p >= 0 ? '+' : '').$p.'% vs previous', $p >= 0 ? 'up' : 'down'];
    };
    [$dTotal, $dTotalDir] = $delta($total, $prevTotal);
    [$dPaid, $dPaidDir] = $delta($paidCount, $prevPaid);
    $attention = $stale + $failedSync;
@endphp

{{-- needs attention --}}
@if($attention > 0)
<div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-950" role="status">
    <p class="font-semibold">Needs your attention</p>
    <ul class="mt-2 space-y-1">
        @if($stale > 0)<li>{{ $stale }} new {{ Str::plural('enquiry', $stale) }} {{ $stale === 1 ? 'has' : 'have' }} waited more than 24 hours. <a class="underline" href="{{ route('admin.enquiries.index', ['stale' => 1]) }}">Open {{ $stale === 1 ? 'it' : 'them' }}</a></li>@endif
        @if($failedSync > 0)<li>{{ $failedSync }} {{ Str::plural('enquiry', $failedSync) }} failed to sync to GoHighLevel or the Google Sheet. <a class="underline" href="{{ route('admin.enquiries.index', ['sync' => 'failed']) }}">Review and retry</a></li>@endif
    </ul>
</div>
@endif

{{-- KPIs --}}
<div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
    <div class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5">
        <p class="text-sm text-muted">Enquiries, last {{ $range }} days</p>
        <p class="font-display text-4xl text-brand sm:text-5xl">{{ $total }}</p>
        @if($dTotal)<p class="mt-1 text-sm {{ $dTotalDir === 'up' ? 'text-green-700' : 'text-red-700' }}">{{ $dTotal }}</p>@endif
    </div>
    <a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="rounded-xl border border-line bg-white p-4 shadow-sm hover:border-brand sm:p-5">
        <p class="text-sm text-muted">New, not yet actioned</p>
        <p class="font-display text-4xl text-brand sm:text-5xl">{{ $newCount }}</p>
        <p class="mt-1 text-sm text-muted">Open the inbox &rarr;</p>
    </a>
    <div class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5">
        <p class="text-sm text-muted">From Google Ads</p>
        <p class="font-display text-4xl text-brand sm:text-5xl">{{ $paidCount }}</p>
        <p class="mt-1 text-sm {{ $dPaid ? ($dPaidDir === 'up' ? 'text-green-700' : 'text-red-700') : 'text-muted' }}">{{ $dPaid ?: ($total ? round($paidCount / $total * 100).'% of enquiries' : 'No enquiries yet') }}</p>
    </div>
    <div class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5">
        <p class="text-sm text-muted">Won</p>
        <p class="font-display text-4xl text-brand sm:text-5xl">{{ $won }}</p>
        <p class="mt-1 text-sm text-muted">{{ $winRate }}% of enquiries in this period</p>
    </div>
</div>

{{-- chart + status --}}
<div class="mt-6 grid gap-6 lg:grid-cols-3">
    <section class="min-w-0 rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5 lg:col-span-2" aria-labelledby="chart-h">
        <h2 id="chart-h" class="text-2xl font-display">Enquiries per day</h2>
        @if($total === 0)
            <p class="py-10 text-center text-muted">No enquiries in this period yet.</p>
        @else
            @php $n = $days->count(); $bw = 600 / $n; @endphp
            <svg viewBox="0 0 600 170" class="mt-3 w-full" role="img" aria-label="Bar chart of enquiries per day">
                @foreach([0.25, 0.5, 0.75, 1] as $g)<line x1="0" x2="600" y1="{{ 140 - 120 * $g }}" y2="{{ 140 - 120 * $g }}" stroke="#E5E1DA" stroke-width="1"/>@endforeach
                @foreach($days->values() as $i => $d)
                    @php $h = $d['count'] ? max(3, 120 * $d['count'] / $maxDay) : 0; @endphp
                    <rect x="{{ $i * $bw + $bw * 0.15 }}" y="{{ 140 - $h }}" width="{{ $bw * 0.7 }}" height="{{ $h }}" rx="2" fill="#0F3B59"><title>{{ $d['label'] }}: {{ $d['count'] }}</title></rect>
                @endforeach
                <text x="0" y="162" font-size="14" fill="#6b6b6b">{{ $days->first()['label'] }}</text>
                <text x="600" y="162" font-size="14" fill="#6b6b6b" text-anchor="end">{{ $days->last()['label'] }}</text>
                <text x="600" y="14" font-size="14" fill="#6b6b6b" text-anchor="end">max {{ $maxDay }} / day</text>
            </svg>
        @endif
    </section>

    <section class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5" aria-labelledby="funnel-h">
        <h2 id="funnel-h" class="text-2xl font-display">Pipeline</h2>
        <ul class="mt-3 space-y-3 text-sm">
            @foreach($statuses as $s => $c)
                <li>
                    <div class="flex justify-between"><a class="capitalize text-brand underline" href="{{ route('admin.enquiries.index', ['status' => $s]) }}">{{ $s }}</a><span>{{ $c }}</span></div>
                    <div class="mt-1 h-2 rounded-full bg-brand-soft"><div class="h-2 rounded-full {{ $s === 'won' ? 'bg-green-600' : ($s === 'lost' ? 'bg-red-400' : 'bg-brand') }}" style="width: {{ $total ? round($c / $total * 100) : 0 }}%"></div></div>
                </li>
            @endforeach
        </ul>
    </section>
</div>

{{-- breakdowns --}}
<div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
    @foreach([['Where they came from', $bySource, 'Source / medium (latest touch)'], ['Campaigns', $byCampaign, 'utm_campaign'], ['Forms', $byForm, 'Which form was used'], ['Popular journeys', $byPackage, 'Enquiries naming a package']] as [$title, $rows, $hint])
        <section class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5">
            <h2 class="text-xl font-display">{{ $title }}</h2>
            <p class="text-xs text-muted">{{ $hint }}</p>
            @if($rows->isEmpty())
                <p class="mt-4 text-sm text-muted">Nothing yet.</p>
            @else
                @php $mx = max(1, $rows->max()); @endphp
                <ul class="mt-3 space-y-2.5 text-sm">
                    @foreach($rows as $label => $c)
                        <li>
                            <div class="flex justify-between gap-3"><span class="truncate" title="{{ $label }}">{{ $label }}</span><b>{{ $c }}</b></div>
                            <div class="mt-1 h-1.5 rounded-full bg-brand-soft"><div class="h-1.5 rounded-full bg-accent" style="width: {{ round($c / $mx * 100) }}%"></div></div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endforeach
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    {{-- recent --}}
    <section class="min-w-0 xl:col-span-2" aria-labelledby="recent-h">
        <div class="mb-3 flex items-end justify-between"><h2 id="recent-h" class="text-3xl font-display">Recent enquiries</h2><a class="text-sm text-brand underline" href="{{ route('admin.enquiries.index') }}">View all</a></div>
        <div class="overflow-x-auto rounded-xl border border-line bg-white">
            <table class="w-full text-sm">
                <thead class="bg-sand/60 text-left"><tr><th class="p-3">When</th><th class="p-3">Name</th><th class="hidden p-3 sm:table-cell">Form</th><th class="hidden p-3 md:table-cell">Source</th><th class="p-3">Status</th></tr></thead>
                <tbody>
                @forelse($recent as $e)
                    <tr class="border-t border-line">
                        <td class="p-3 whitespace-nowrap" title="{{ $e->created_at->format('d M Y H:i') }}">{{ $e->created_at->diffForHumans() }}</td>
                        <td class="p-3"><a class="text-brand underline" href="{{ route('admin.enquiries.show', $e) }}">{{ $e->full_name }}</a><span class="block text-xs text-muted">{{ $e->package_name ?: $e->email }}</span></td>
                        <td class="hidden p-3 sm:table-cell">{{ \App\Support\SheetRow::TABS[$e->form_name] ?? $e->form_name }}</td>
                        <td class="hidden p-3 md:table-cell">{{ \App\Http\Controllers\Admin\DashboardController::sourceLabel($e) }}</td>
                        <td class="p-3"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs capitalize {{ ['new' => 'bg-amber-100 text-amber-900', 'contacted' => 'bg-blue-100 text-blue-900', 'quoted' => 'bg-indigo-100 text-indigo-900', 'won' => 'bg-green-100 text-green-900', 'lost' => 'bg-red-100 text-red-900'][$e->status] ?? 'bg-sand' }}">{{ $e->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-muted">No enquiries yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- setup checklist --}}
    <section class="min-w-0" aria-labelledby="setup-h">
        <h2 id="setup-h" class="mb-3 text-3xl font-display">Site health</h2>
        <ul class="divide-y divide-line rounded-xl border border-line bg-white text-sm">
            @foreach($checks as $c)
                <li class="flex items-start gap-3 p-3">
                    <span class="mt-0.5 inline-grid h-5 w-5 flex-none place-items-center rounded-full text-xs font-bold {{ $c['ok'] ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}" aria-hidden="true">{{ $c['ok'] ? '✓' : '!' }}</span>
                    <div class="min-w-0">
                        <p class="{{ $c['ok'] ? '' : 'font-semibold' }}">{{ $c['label'] }}<span class="sr-only">: {{ $c['ok'] ? 'done' : 'needs attention' }}</span></p>
                        @unless($c['ok'])<p class="text-muted">{{ $c['hint'] }} <a class="text-brand underline" href="{{ route($c['route']) }}">Fix</a></p>@endunless
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="mt-4 flex flex-wrap gap-2">
            <x-button :href="route('admin.packages.create')" variant="outline" class="!px-4 !py-2">New package</x-button>
            <x-button :href="route('admin.posts.index')" variant="outline" class="!px-4 !py-2">Journal</x-button>
            <x-button :href="route('home')" variant="outline" class="!px-4 !py-2" target="_blank">View site</x-button>
        </div>
    </section>
</div>
@endsection

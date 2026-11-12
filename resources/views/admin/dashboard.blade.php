@extends('admin.layout')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="grid gap-4 sm:grid-cols-3">
    @foreach([['Enquiries this week', $thisWeek], ['New / unactioned', $new], ['All enquiries', $total]] as [$label, $n])
        <div class="rounded-xl bg-white p-5 shadow-sm border border-line"><p class="text-sm text-muted">{{ $label }}</p><p class="font-display text-5xl text-brand">{{ $n }}</p></div>
    @endforeach
</div>

<h2 class="mt-10 mb-3 text-3xl">Recent enquiries</h2>
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="w-full text-sm">
        <thead class="bg-sand text-left"><tr><th class="p-3">Date</th><th class="p-3">Name</th><th class="p-3">Form</th><th class="p-3">Status</th></tr></thead>
        <tbody>
        @forelse($recent as $e)
            <tr class="border-t border-line"><td class="p-3 whitespace-nowrap">{{ $e->created_at->format('d M H:i') }}</td>
                <td class="p-3"><a class="underline text-brand" href="{{ route('admin.enquiries.show', $e) }}">{{ $e->full_name }}</a></td>
                <td class="p-3">{{ $e->form_name }}</td><td class="p-3 capitalize">{{ $e->status }}</td></tr>
        @empty
            <tr><td colspan="4" class="p-6 text-center text-muted">No enquiries yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-8 flex flex-wrap gap-3">
    <x-button :href="route('admin.enquiries.index')" variant="secondary">Open inbox</x-button>
    <x-button :href="route('admin.packages.create')" variant="outline">New package</x-button>
    <x-button :href="route('admin.settings.edit')" variant="outline">Site settings</x-button>
</div>
@endsection

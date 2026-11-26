@extends('admin.layout')
@section('title', 'Pages')
@section('heading', 'Pages')
@section('actions')<x-button :href="route('admin.pages.create')" variant="secondary">+ Add page</x-button>@endsection

@section('content')
<div class="space-y-8">
    <section>
        <h2 class="mb-1 text-2xl">Your pages</h2>
        <p class="mb-4 text-sm text-muted">Pages you build from sections. They live at goaustriatours.com/your-address.</p>
        @if($custom->isEmpty())
            <div class="rounded-xl border border-dashed border-secondary bg-white p-8 text-center">
                <p class="mb-4 text-muted">No pages yet. Create a landing page, a guide or an offer page in a few minutes.</p>
                <x-button :href="route('admin.pages.create')" variant="secondary">Create your first page</x-button>
            </div>
        @else
            <div class="grid gap-3">
                @foreach($custom as $p)
                    <article class="flex flex-wrap items-center gap-3 rounded-xl border border-line bg-white p-4">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-display text-xl text-brand">{{ $p->title }}</h3>
                            <p class="text-sm text-muted">/{{ $p->slug }} &middot; {{ count($p->blocks ?? []) }} {{ count($p->blocks ?? []) === 1 ? 'section' : 'sections' }}@if($p->nav_location) &middot; {{ \App\Models\Page::NAV_LOCATIONS[$p->nav_location] ?? '' }}@endif &middot; updated {{ $p->updated_at?->diffForHumans() }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $p->is_published ? 'bg-green-100 text-green-900' : 'bg-amber-100 text-amber-900' }}">{{ $p->is_published ? 'Published' : 'Draft' }}</span>
                        @if($p->noindex)<span class="rounded-full bg-sand px-3 py-1 text-xs">Hidden from Google</span>@endif
                        <div class="flex flex-wrap gap-2">
                            <a class="rounded border border-line px-3 py-1.5 text-sm" href="{{ $p->url() }}" target="_blank" rel="noopener">{{ $p->is_published ? 'View' : 'Preview' }}</a>
                            <a class="rounded bg-brand px-4 py-1.5 text-sm text-white" href="{{ route('admin.pages.edit', $p) }}">Edit</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section>
        <h2 class="mb-1 text-2xl">Built-in pages</h2>
        <p class="mb-4 text-sm text-muted">Pages the site needs. Their address is fixed and they cannot be deleted. About, Terms and Privacy can also take extra sections.</p>
        <div class="grid gap-3 md:grid-cols-2">
            @foreach($system as $p)
                <article class="flex items-center gap-3 rounded-xl border border-line bg-white p-4">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-display text-lg text-brand">{{ $p->title }}</h3>
                        <p class="text-xs text-muted">{{ preg_replace('#^https?://[^/]+#', '', $p->url()) ?: '/' }}@if($p->noindex) &middot; hidden from Google @endif</p>
                    </div>
                    @if($p->slug !== 'thank-you')<a class="rounded border border-line px-3 py-1.5 text-sm" href="{{ $p->url() }}" target="_blank" rel="noopener">View</a>@endif
                    <a class="rounded bg-brand px-4 py-1.5 text-sm text-white" href="{{ route('admin.pages.edit', $p) }}">Edit</a>
                </article>
            @endforeach
        </div>
    </section>
    <p class="text-sm text-muted">Testimonials, FAQs and the Journal have their own menu items.</p>
</div>
@endsection

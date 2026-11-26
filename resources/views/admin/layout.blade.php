<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | {{ \App\Models\Setting::get('brand_name', 'Admin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="bg-sand/60 text-ink" x-data="{ nav: false }">
@php
    // Sidebar sections. Only what this user's role may open is shown (App\Support\Roles); empty sections disappear.
    $groups = [
        'overview' => ['Overview', [['admin.dashboard', 'Dashboard'], ['admin.enquiries.index', 'Enquiries']]],
        'website' => ['Website', [['admin.pages.index', 'Pages'], ['admin.menus.edit', 'Menus'], ['admin.design.edit', 'Design'], ['admin.media.index', 'Media']]],
        'tours' => ['Tours', [['admin.packages.index', 'Packages'], ['admin.tour-types.index', 'Tour types'], ['admin.destinations.index', 'Destinations'], ['admin.quiz.index', 'Quiz']]],
        'stories' => ['Stories', [['admin.posts.index', 'Journal'], ['admin.testimonials.index', 'Testimonials'], ['admin.faqs.index', 'FAQs']]],
        'settings' => ['Settings', [['admin.settings.edit', 'Settings'], ['admin.trust.edit', 'Trust & tracking'], ['admin.ghl.edit', 'GoHighLevel'], ['admin.sheets.edit', 'Google Sheet'], ['admin.users.index', 'Admin users']]],
    ];
    $me = auth('admin')->user();
    $groups = array_filter(array_map(fn ($g) => [$g[0], array_values(array_filter($g[1], fn ($l) => \App\Support\Roles::can($me, $l[0])))], $groups), fn ($g) => count($g[1]) > 0);
    $isOn = fn (string $r) => request()->routeIs($r === 'admin.dashboard' ? $r : Str::beforeLast($r, '.').'*');
    // Thin-line sidebar icons (24px grid, 1.25 stroke), one per screen, same style as the public site's icons.
    $navIco = [
        'admin.dashboard' => '<rect x="3.5" y="3.5" width="7" height="8" rx="1.2"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.2"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.2"/><rect x="3.5" y="14.5" width="7" height="6" rx="1.2"/>',
        'admin.enquiries.index' => '<path d="M21.5 12.5h-5.5l-1.8 2.6h-4.4L8 12.5H2.5"/><path d="M5.6 5.2 2.5 12.5v5.8a1.8 1.8 0 0 0 1.8 1.8h15.4a1.8 1.8 0 0 0 1.8-1.8v-5.8l-3.1-7.3a1.8 1.8 0 0 0-1.6-1.2H7.2a1.8 1.8 0 0 0-1.6 1.2z"/>',
        'admin.pages.index' => '<path d="M14 3.5H6.8A1.8 1.8 0 0 0 5 5.3v13.4a1.8 1.8 0 0 0 1.8 1.8h10.4a1.8 1.8 0 0 0 1.8-1.8V8.5z"/><path d="M14 3.5v5h5M8.5 12.5h7M8.5 16h4.5"/>',
        'admin.menus.edit' => '<path d="M4 6.5h16M4 12h16M4 17.5h10"/>',
        'admin.design.edit' => '<path d="M12 3a9 9 0 0 0 0 18c.9 0 1.4-.7 1.4-1.4 0-.4-.1-.7-.4-1-.2-.3-.4-.6-.4-1 0-.8.7-1.5 1.5-1.5h1.8A5.1 5.1 0 0 0 21 11c0-4.4-4-8-9-8z"/><circle cx="7.5" cy="11.5" r=".9"/><circle cx="10" cy="7.5" r=".9"/><circle cx="14.5" cy="7.5" r=".9"/><circle cx="17" cy="11" r=".9"/>',
        'admin.media.index' => '<rect x="3.5" y="4" width="17" height="16" rx="1.8"/><circle cx="9" cy="9.5" r="1.8"/><path d="m20.5 15.5-4.2-4.2a1.6 1.6 0 0 0-2.3 0L5.5 20"/>',
        'admin.packages.index' => '<rect x="3.5" y="7.5" width="17" height="12.5" rx="1.8"/><path d="M8.5 7.5V5.6a1.6 1.6 0 0 1 1.6-1.6h3.8a1.6 1.6 0 0 1 1.6 1.6v1.9M3.5 13h17"/>',
        'admin.tour-types.index' => '<circle cx="12" cy="12" r="8.5"/><path d="m15.2 8.8-1.9 4.5-4.5 1.9 1.9-4.5z"/>',
        'admin.destinations.index' => '<path d="M12 20.5s6.5-5.8 6.5-10.3a6.5 6.5 0 0 0-13 0c0 4.5 6.5 10.3 6.5 10.3z"/><circle cx="12" cy="10.2" r="2.3"/>',
        'admin.quiz.index' => '<path d="m3.5 6.5 1.8 1.8 3.2-3.3M3.5 15.5l1.8 1.8 3.2-3.3M12.5 6.8h8M12.5 15.8h8"/>',
        'admin.posts.index' => '<path d="M2.5 4.5h5.8A3.7 3.7 0 0 1 12 8.2v12.3a2.8 2.8 0 0 0-2.8-2.8H2.5z"/><path d="M21.5 4.5h-5.8A3.7 3.7 0 0 0 12 8.2v12.3a2.8 2.8 0 0 1 2.8-2.8h6.7z"/>',
        'admin.testimonials.index' => '<path d="M20.5 14.5a1.8 1.8 0 0 1-1.8 1.8H8l-4.5 4V5.3a1.8 1.8 0 0 1 1.8-1.8h13.4a1.8 1.8 0 0 1 1.8 1.8z"/><path d="M8.5 9.5h7M8.5 12.5h4"/>',
        'admin.faqs.index' => '<circle cx="12" cy="12" r="8.5"/><path d="M9.6 9.3a2.5 2.5 0 1 1 3.4 2.3c-.6.3-1 .8-1 1.5v.5M12 16.8h.01"/>',
        'admin.settings.edit' => '<path d="M4 20.5v-6.5M4 10V3.5M12 20.5V12M12 8V3.5M20 20.5V16M20 12V3.5M1.8 14h4.4M9.8 8h4.4M17.8 16h4.4"/>',
        'admin.trust.edit' => '<path d="M12 21s7.5-3.6 7.5-9.4V5.4L12 2.6 4.5 5.4v6.2C4.5 17.4 12 21 12 21z"/><path d="m9 11.8 2.1 2.1 3.9-3.9"/>',
        'admin.ghl.edit' => '<path d="M10 13.5a4.6 4.6 0 0 0 6.9.5l2.8-2.8a4.6 4.6 0 0 0-6.5-6.5l-1.6 1.6"/><path d="M14 10.5a4.6 4.6 0 0 0-6.9-.5l-2.8 2.8a4.6 4.6 0 0 0 6.5 6.5l1.6-1.6"/>',
        'admin.sheets.edit' => '<rect x="3.5" y="3.5" width="17" height="17" rx="1.8"/><path d="M3.5 9.2h17M3.5 14.8h17M9.2 3.5v17"/>',
        'admin.users.index' => '<path d="M15.5 20.5v-1.8a3.7 3.7 0 0 0-3.7-3.7H6.2a3.7 3.7 0 0 0-3.7 3.7v1.8"/><circle cx="9" cy="7.7" r="3.6"/><path d="M21.5 20.5v-1.8a3.7 3.7 0 0 0-2.8-3.6M15.6 4.2a3.6 3.6 0 0 1 0 7"/>',
        'site' => '<path d="M14.5 3.5h6v6M10 14 20.5 3.5M18 13.5v5.2a1.8 1.8 0 0 1-1.8 1.8H5.3a1.8 1.8 0 0 1-1.8-1.8V7.8A1.8 1.8 0 0 1 5.3 6h5.2"/>',
        'logout' => '<path d="M9 20.5H5.3a1.8 1.8 0 0 1-1.8-1.8V5.3a1.8 1.8 0 0 1 1.8-1.8H9M15.5 16.5 20 12l-4.5-4.5M20 12H9"/>',
    ];
    $ico = fn (string $k) => '<svg class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($navIco[$k] ?? '').'</svg>';
@endphp
<div class="lg:flex min-h-screen">
    <aside class="sticky top-0 z-40 bg-brand-dark text-white lg:static lg:min-h-screen lg:w-60 lg:shrink-0">
        <div class="flex items-center justify-between px-4 py-2.5 lg:block lg:py-3">
            <a href="{{ $me ? \App\Support\Roles::home($me) : route('admin.login') }}" class="font-display text-2xl">Admin</a>
            <button class="grid size-11 place-items-center rounded-lg hover:bg-white/10 lg:hidden" @click="nav = !nav" :aria-expanded="nav" aria-controls="admin-nav" aria-label="Menu"><x-icon name="menu" /></button>
        </div>
        <nav id="admin-nav" class="hidden px-2 pb-4 max-lg:absolute max-lg:inset-x-0 max-lg:top-full max-lg:max-h-[calc(100vh-4rem)] max-lg:overflow-y-auto max-lg:bg-brand-dark max-lg:shadow-2xl lg:block" :class="{ '!block': nav }" aria-label="Admin">
            {{-- Collapsible sections: the one you are in is always open; the others remember open/closed in this browser (Overview starts open). --}}
            @foreach($groups as $gk => [$gLabel, $gLinks])
                @php
                    $gActive = collect($gLinks)->contains(fn ($l) => $isOn($l[0]));
                    $gStart = $gActive ? 'true' : "(() => { try { const v = localStorage.getItem('adm-nav-{$gk}'); return v === null ? ".($gk === 'overview' ? 'true' : 'false')." : v === '1'; } catch (e) { return true; } })()";
                @endphp
                <div class="mt-1 first:mt-0" x-data="{ open: {{ $gStart }} }" x-init="$watch('open', (v) => { try { localStorage.setItem('adm-nav-{{ $gk }}', v ? '1' : '0'); } catch (e) {} })">
                    <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 pb-1.5 pt-3 text-[11px] font-semibold uppercase tracking-[0.16em] hover:text-white/90 {{ $gActive ? 'text-cta' : 'text-white/55' }}" @click="open = ! open" :aria-expanded="open" aria-controls="adm-g-{{ $gk }}">
                        <span>{{ $gLabel }}</span>
                        <svg class="size-3.5 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="adm-g-{{ $gk }}" x-show="open" @if(! $gActive) x-cloak @endif>
                        @foreach($gLinks as [$r, $label])
                            <a href="{{ route($r) }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm hover:bg-white/10 lg:py-1.5 {{ $isOn($r) ? 'bg-white/15 font-semibold' : 'text-white/90' }}" @if($isOn($r)) aria-current="page" @endif><span class="{{ $isOn($r) ? 'text-cta' : 'text-white/60 group-hover:text-white' }}">{!! $ico($r) !!}</span>{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="mt-3 border-t border-white/10 pt-2"></div>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="group flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-white/85 hover:bg-white/10 lg:py-2"><span class="text-white/60 group-hover:text-white">{!! $ico('site') !!}</span>View site</a>
            @if($me)<p class="mt-3 border-t border-white/10 px-3 pt-3 text-xs text-white/70">{{ $me->name }}<span class="ml-2 rounded-full bg-white/15 px-2 py-0.5 text-[11px] font-semibold text-white">{{ \App\Support\Roles::label($me->role) }}</span></p>@endif
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="group mt-1 flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left text-sm text-white/85 hover:bg-white/10 lg:py-2"><span class="text-white/60 group-hover:text-white">{!! $ico('logout') !!}</span>Log out</button></form>
        </nav>
    </aside>

    <main class="flex-1 p-4 sm:p-8 min-w-0">
        <header class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="font-display text-[32px]">@yield('heading')</h1>
            <div>@yield('actions')</div>
        </header>
        @if(session('status'))<p class="mb-4 rounded-lg border border-green-300 bg-green-50 p-3 text-sm text-green-900" role="status">{{ session('status') }}</p>@endif
        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-900" role="alert">
                <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>

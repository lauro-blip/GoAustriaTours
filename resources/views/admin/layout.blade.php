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
    $links = [
        ['admin.dashboard', 'Dashboard'], ['admin.enquiries.index', 'Enquiries'], ['admin.packages.index', 'Packages'],
        ['admin.tour-types.index', 'Tour types'], ['admin.destinations.index', 'Destinations'], ['admin.quiz.index', 'Quiz'],
        ['admin.pages.index', 'Pages'], ['admin.testimonials.index', 'Testimonials'], ['admin.faqs.index', 'FAQs'], ['admin.posts.index', 'Journal'],
        ['admin.media.index', 'Media'], ['admin.menus.edit', 'Menus'], ['admin.design.edit', 'Design'], ['admin.settings.edit', 'Settings'], ['admin.trust.edit', 'Trust & tracking'], ['admin.ghl.edit', 'GoHighLevel'], ['admin.sheets.edit', 'Google Sheet'], ['admin.users.index', 'Admin users'],
    ];
    // Only show what this user's role may open (App\Support\Roles).
    $me = auth('admin')->user();
    $links = array_values(array_filter($links, fn ($l) => \App\Support\Roles::can($me, $l[0])));
@endphp
<div class="lg:flex min-h-screen">
    <aside class="sticky top-0 z-40 bg-brand-dark text-white lg:static lg:min-h-screen lg:w-60 lg:shrink-0">
        <div class="flex items-center justify-between px-4 py-2.5 lg:block lg:py-3">
            <a href="{{ $me ? \App\Support\Roles::home($me) : route('admin.login') }}" class="font-display text-2xl">Admin</a>
            <button class="grid size-11 place-items-center rounded-lg hover:bg-white/10 lg:hidden" @click="nav = !nav" :aria-expanded="nav" aria-controls="admin-nav" aria-label="Menu"><x-icon name="menu" /></button>
        </div>
        <nav id="admin-nav" class="hidden px-2 pb-4 max-lg:absolute max-lg:inset-x-0 max-lg:top-full max-lg:max-h-[calc(100vh-4rem)] max-lg:overflow-y-auto max-lg:bg-brand-dark max-lg:shadow-2xl lg:block" :class="{ '!block': nav }" aria-label="Admin">
            @foreach($links as [$r, $label])
                <a href="{{ route($r) }}" class="block rounded-lg px-3 py-3 text-sm hover:bg-white/10 lg:py-2 {{ request()->routeIs($r === 'admin.dashboard' ? $r : Str::beforeLast($r, '.').'*') ? 'bg-white/15 font-semibold' : '' }}">{{ $label }}</a>
            @endforeach
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="mt-3 block rounded-lg px-3 py-3 text-sm text-white/85 hover:bg-white/10 lg:py-2">View site &nearr;</a>
            @if($me)<p class="mt-3 border-t border-white/10 px-3 pt-3 text-xs text-white/70">{{ $me->name }}<span class="ml-2 rounded-full bg-white/15 px-2 py-0.5 text-[11px] font-semibold text-white">{{ \App\Support\Roles::label($me->role) }}</span></p>@endif
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="mt-1 w-full rounded-lg px-3 py-3 text-left text-sm text-white/85 hover:bg-white/10 lg:py-2">Log out</button></form>
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

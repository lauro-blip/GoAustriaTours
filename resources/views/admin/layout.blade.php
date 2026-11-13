<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | {{ \App\Models\Setting::get('brand_name', 'Admin') }}</title>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="bg-sand/60 text-ink" x-data="{ nav: false }">
@php
    $links = [
        ['admin.dashboard', 'Dashboard'], ['admin.enquiries.index', 'Enquiries'], ['admin.packages.index', 'Packages'],
        ['admin.tour-types.index', 'Tour types'], ['admin.destinations.index', 'Destinations'], ['admin.quiz.index', 'Quiz'],
        ['admin.pages.index', 'Pages'], ['admin.testimonials.index', 'Testimonials'], ['admin.faqs.index', 'FAQs'], ['admin.posts.index', 'Journal'],
        ['admin.media.index', 'Media'], ['admin.settings.edit', 'Settings'], ['admin.users.index', 'Admin users'],
    ];
@endphp
<div class="lg:flex min-h-screen">
    <aside class="bg-brand-dark text-white lg:w-60 lg:shrink-0">
        <div class="flex items-center justify-between px-4 py-3 lg:block">
            <a href="{{ route('admin.dashboard') }}" class="font-display text-2xl">Admin</a>
            <button class="lg:hidden p-2" @click="nav = !nav" :aria-expanded="nav" aria-label="Menu"><x-icon name="menu" /></button>
        </div>
        <nav class="px-2 pb-4 lg:block" :class="nav ? 'block' : 'hidden'" aria-label="Admin">
            @foreach($links as [$r, $label])
                <a href="{{ route($r) }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-white/10 {{ request()->routeIs(Str::beforeLast($r, '.').'*') ? 'bg-white/15 font-semibold' : '' }}">{{ $label }}</a>
            @endforeach
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="mt-3 block rounded-lg px-3 py-2 text-sm text-white/70 hover:bg-white/10">View site &nearr;</a>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="mt-1 w-full rounded-lg px-3 py-2 text-left text-sm text-white/70 hover:bg-white/10">Log out</button></form>
        </nav>
    </aside>

    <main class="flex-1 p-4 sm:p-8 min-w-0">
        <header class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="font-display text-4xl">@yield('heading')</h1>
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

<footer class="bg-brand-dark text-white/80 mt-auto">
    <div class="container-page py-12 grid gap-10 md:grid-cols-3">
        <div>
            <p class="font-display text-3xl text-white">{{ $site['brand_name'] ?? '' }}</p>
            <p class="mt-3 text-sm">{{ $site['footer_text'] ?? '' }}</p>
        </div>
        <div>
            <h2 class="font-sans text-sm font-semibold uppercase tracking-wider text-white">Explore</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach($navTourTypes as $t)<li><a class="hover:underline" href="{{ route('tours.show', $t->slug) }}">{{ $t->name }} Tours</a></li>@endforeach
                <li><a class="hover:underline" href="{{ route('packages.index') }}">All packages</a></li>
                <li><a class="hover:underline" href="{{ route('about') }}">About us</a></li>
                <li><a class="hover:underline" href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </div>
        <div>
            <h2 class="font-sans text-sm font-semibold uppercase tracking-wider text-white">Contact</h2>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a class="hover:underline" href="tel:{{ $site['phone_tel'] ?? '' }}">{{ $site['phone'] ?? '' }}</a></li>
                <li><a class="hover:underline" href="mailto:{{ $site['email'] ?? '' }}">{{ $site['email'] ?? '' }}</a></li>
                <li>{{ $site['address'] ?? '' }}</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="container-page py-5 flex flex-wrap items-center justify-between gap-3 text-xs">
            <p>&copy; {{ date('Y') }} {{ $site['legal_company_name'] ?? '' }}. All rights reserved.</p>
            <p class="flex gap-4">
                <a class="hover:underline" href="{{ route('terms') }}">Terms &amp; Conditions</a>
                <a class="hover:underline" href="{{ route('privacy') }}">Privacy Policy</a>
            </p>
        </div>
    </div>
</footer>

{{-- About Us mega menu (dropdown panel of a built-in header item; data from the layouts.site composer). --}}
<div class="mega">
    <div class="mega-in ab">
        <div>
            <h5>About us</h5>
            <ul class="mega-links">
                <li><a href="{{ route('about') }}"><b>About {{ $brand }}</b><em>Who we are and how we plan your trip</em></a></li>
                <li><a href="{{ route('journal.index') }}"><b>Journal</b><em>Travel ideas and guides to Austria</em></a></li>
                <li><a href="{{ route('testimonials') }}"><b>Guest stories</b><em>What our travellers say</em></a></li>
                @foreach($navPages['about'] ?? [] as $np)<li><a href="{{ route('pages.show', $np->slug) }}"><b>{{ $np->title }}</b>@if($np->intro)<em>{{ \Illuminate\Support\Str::limit($np->intro, 48) }}</em>@endif</a></li>@endforeach
            </ul>
            <p class="mega-legal"><a href="{{ route('terms') }}">Terms &amp; Conditions</a><a href="{{ route('privacy') }}">Privacy Policy</a><a href="{{ route('photo-credits') }}">Photo credits</a></p>
        </div>
        @if($navPost)
        <div>
            <h5>From the journal</h5>
            <article class="mega-feat mega-post">
                <a class="mf-img" href="{{ route('journal.show', $navPost) }}" tabindex="-1" aria-hidden="true"><img src="{{ $navPost->imageUrl() }}" alt="" loading="lazy">@if($navPost->category)<span class="mf-tag">{{ $navPost->category }}</span>@endif</a>
                <div class="mf-body">
                    <h5><a href="{{ route('journal.show', $navPost) }}">{{ $navPost->title }}</a></h5>
                    <a class="more" href="{{ route('journal.show', $navPost) }}">Read article &rarr;</a>
                </div>
            </article>
        </div>
        @endif
        <aside class="mega-help" aria-label="Get in touch">
            <p class="mp-k">Get in touch</p>
            <h5>Talk to a travel specialist</h5>
            <p class="mh-lead">Tell us about the trip you have in mind and we will reply with a free quote.</p>
            <a class="btn sm" href="{{ route('contact') }}" data-quote>Request a quote <i aria-hidden="true">&rarr;</i></a>
            @if($phone && ! str_contains($phone, '[CONFIRM'))<a class="mh-call" href="tel:{{ $tel }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg>{{ $phone }}</a>@endif
        </aside>
    </div>
</div>

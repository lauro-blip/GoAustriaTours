{{-- Destinations mega menu (dropdown panel of a built-in header item; data from the layouts.site composer).
     Destinations are PLACES (App\Support\Places::withJourneys), no regions: a row of 4 photo cards (most-visited places we hold a photo of),
     every place below in 3 columns sorted by journey count, and the featured journey on the right. Styles: "Destinations mega menu" in home.css. --}}
@php
    $byCount = collect($navPlaces)->sortByDesc('count');
    $topPlaces = $byCount->filter(fn ($pl) => $pl['imageUrl'])->take(4);
@endphp
<div class="mega">
    <div class="mega-in dmm">
        <div class="dmm-main">
            <div class="dmm-head"><h5>Top destinations</h5><a class="more" href="{{ route('destinations.index') }}">Map of all destinations &rarr;</a></div>
            <div class="dmm-cards">
                @foreach($topPlaces as $key => $pl)
                    <a href="{{ route('packages.index', ['place' => $key]) }}"><img src="{{ $pl['imageUrl'] }}" alt="" loading="lazy" width="400" height="300"><span><b>{{ $pl['name'] }}</b><small>{{ $pl['count'] }} {{ $pl['count'] === 1 ? 'journey' : 'journeys' }}</small></span></a>
                @endforeach
            </div>
            <h5 class="dmm-sub">All destinations</h5>
            <ul class="dmm-list">
                @foreach($byCount as $key => $pl)<li><a href="{{ route('packages.index', ['place' => $key]) }}"><span>{{ $pl['name'] }}</span><small>{{ $pl['count'] }}</small></a></li>@endforeach
            </ul>
        </div>
        @if($navFeatured)
        @php $nf = $navFeatured; $np = $nf->p; @endphp
        <aside class="mega-feat" aria-label="Featured journey">
            <a class="mf-img" href="{{ route('packages.show', $np) }}" tabindex="-1" aria-hidden="true"><img src="{{ $nf->image }}" alt="" loading="lazy">@if($np->tourType)<span class="mf-tag">{{ $np->tourType->name }}</span>@endif</a>
            <div class="mf-body">
                <p class="mp-k">Featured journey</p>
                <h5><a href="{{ route('packages.show', $np) }}">{{ $np->title }}</a></h5>
                <p class="mf-meta">@if($np->nights)<b>{{ $np->nights + 1 }}</b> days &middot; <b>{{ $np->nights }}</b> nights @endif @if($nf->price)<span>From <b>{{ $nf->price }}</b></span>@endif</p>
                <a class="btn sm" href="{{ route('packages.show', $np) }}">View journey <i aria-hidden="true">&rarr;</i></a>
            </div>
        </aside>
        @else
        <aside class="mega-promo"><p class="mp-k">Personal planning</p><h5>Not sure where to start?</h5><p>Answer a few quick questions and a travel specialist will plan your route.</p><a class="btn sm" href="{{ route('contact') }}" data-quote>Plan my journey <i aria-hidden="true">&rarr;</i></a></aside>
        @endif
    </div>
</div>

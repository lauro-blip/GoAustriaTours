{{-- Home: destination tiles (regions from Admin > Destinations, places from config goaustria.nav_places). --}}
@php
    $destImg = config('goaustria.destination_images', []);
    $destTag = ['vienna-salzburg' => 'Imperial palaces and Baroque music', 'tyrol' => 'Alpine valleys, lakes and peaks'];
    $destSixth = ['vienna-salzburg' => 'Melk', 'tyrol' => 'Achensee'];   // both occur in real package itineraries
    $bespokePlaces = ['Vienna', 'Salzburg', 'Hallstatt', 'Innsbruck', 'Zell am See', 'Graz'];
    $pinSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>';
@endphp
<section class="sec" id="destinations">
    <div class="wrap">
        @include('home._head')
        <div class="dgrid three">
            @foreach($destinations->take(6) as $dst)
                @php
                    $pl = collect($navPlaces[$dst->slug] ?? [])->map(fn ($x) => $x[1])->when(isset($destSixth[$dst->slug]), fn ($c) => $c->push($destSixth[$dst->slug]))->unique()->take(6);
                    $tagline = $destTag[$dst->slug] ?? \Illuminate\Support\Str::limit((string) $dst->summary, 38);
                    $n = $pkgs->where('dest', $dst->slug)->count();
                    $di = $destImg[$dst->slug] ?? $winter[($loop->index * 2) % count($winter)];
                @endphp
                <article class="dtile rv">
                    <div class="dt-photo">
                        <img src="{{ $w($di, '-1280') }}" alt="" loading="lazy">
                        @if($n)<p class="dt-kick">{{ $n }} {{ $n === 1 ? 'journey' : 'journeys' }}</p>@endif
                        <div class="dt-title">
                            <h3>{{ $dst->name }}</h3>
                            @if($tagline)<p>{{ $tagline }}</p>@endif
                        </div>
                    </div>
                    <div class="dt-body">
                        @if($pl->isNotEmpty())
                            <ul class="dt-places">@foreach($pl as $x)<li><a href="{{ route('packages.index', ['q' => $x]) }}">{!! $pinSvg !!}{{ $x }}</a></li>@endforeach</ul>
                        @endif
                        <div class="dt-foot">
                            <a class="dt-explore" href="{{ route('packages.index', ['destination' => $dst->slug]) }}">Explore</a>
                            <a class="btn sm" href="{{ route('contact') }}" data-quote data-interest="{{ $dst->name }}">Enquire now</a>
                        </div>
                    </div>
                </article>
            @endforeach
            @if($destinations->take(6)->count() < 3)
                <article class="dtile rv">
                    <div class="dt-photo">
                        <img src="{{ $w('hallstatt', '-1280') }}" alt="" loading="lazy">
                        <p class="dt-kick">Bespoke</p>
                        <div class="dt-title">
                            <h3>Your Own Route</h3>
                            <p>Mix any of these. Tell us your plan.</p>
                        </div>
                    </div>
                    <div class="dt-body">
                        <ul class="dt-places">@foreach($bespokePlaces as $x)<li><a href="{{ route('packages.index', ['q' => $x]) }}">{!! $pinSvg !!}{{ $x }}</a></li>@endforeach</ul>
                        <div class="dt-foot">
                            <a class="dt-explore" href="{{ route('packages.index') }}">Explore</a>
                            <a class="btn sm" href="{{ route('contact') }}" data-quote data-interest="a custom route through Austria">Plan my route</a>
                        </div>
                    </div>
                </article>
            @endif
        </div>
        @if(filled($d['link_label'] ?? null))<p class="dest-foot"><a class="link" href="{{ route('destinations.index') }}">{{ $tx($d['link_label']) }} &rarr;</a></p>@endif
    </div>
</section>

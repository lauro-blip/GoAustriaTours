{{-- Destinations mega menu (dropdown panel of a built-in header item; data from the layouts.site composer). --}}
<div class="mega">
    <div class="mega-in">
        <div>
            <h5>Explore by region</h5>
            <div class="mega-cards">
                @foreach($destinations as $d)
                    @php $dn = $pkgCounts['dest'][$d->slug] ?? 0; @endphp
                    <a href="{{ route('packages.index', ['destination' => $d->slug]) }}"><img src="{{ $w(['vienna-salzburg' => 'hero-salzburg', 'tyrol' => 'hero-montafon'][$d->slug] ?? $winter[$loop->index % count($winter)], '-768') }}" alt="" loading="lazy"><span><b>{{ $d->name }}</b>@if($dn)<small>{{ $dn }} {{ $dn === 1 ? 'journey' : 'journeys' }}</small>@endif</span></a>
                @endforeach
            </div>
        </div>
        <div>
            <h5>Popular places</h5>
            <ul class="mega-list">
                @foreach($navPlaces as $slug => $list)@foreach($list as $pl)<li><a href="{{ route('packages.index', ['q' => $pl[1]]) }}">{{ $pl[0] }}</a></li>@endforeach @endforeach
            </ul>
            <a class="more" href="{{ route('destinations.index') }}">All destinations &rarr;</a>
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

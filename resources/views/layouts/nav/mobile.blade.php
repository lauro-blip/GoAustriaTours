{{-- Mobile menu (under 1100px). Items, labels and order follow Admin > Menus ($mh); submenu content comes from the same data as the
     desktop mega menus. Accordion behaviour in home.js (.sub / .sub-t / .sub-l, one open at a time). Photos are tiny -240 thumbnails,
     lazy-loaded, so they only download when a submenu is opened. --}}
@php
    $thumb = fn (string $name) => asset('images/winter/'.$name.'-240.webp');
    $count = fn (int $n) => $n.' '.($n === 1 ? 'journey' : 'journeys');
    $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
@endphp
<nav class="mobile mob2" aria-label="Mobile" id="mobile-menu">
    <div class="m-top">
        <a class="m-logo" href="{{ route('home') }}" aria-label="{{ $brand }}"><img src="{{ $img('logo-white.webp') }}" alt="{{ $brand }}" width="500" height="167" loading="lazy"></a>
        <button class="close" type="button" data-close-menu aria-label="Close menu"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
    </div>

    <div class="m-list">
    @foreach($mh['items'] as $mi)
        @continue($mi['hidden'])
        @switch($mi['key'])
            @case('home')
                <a class="m-link" href="{{ route('home') }}" data-close-menu>{{ $mi['label'] }}</a>
                @break

            @case('destinations')
                <div class="sub"><button type="button" class="sub-t" aria-expanded="false">{{ $mi['label'] }}<i class="caret"></i></button>
                    <div class="sub-l"><div>
                        <p class="m-k">Top destinations</p>
                        <div class="m-cards">
                            @foreach(collect($navPlaces)->filter(fn ($pl) => $pl['imageUrl'])->sortByDesc('count')->take(4) as $key => $pl)
                                <a class="m-card" href="{{ route('packages.index', ['place' => $key]) }}">
                                    <img src="{{ \App\Support\Places::photo($key, 'sm') }}" alt="" loading="lazy" width="240" height="160">
                                    <span><b>{{ $pl['name'] }}</b><small>{{ $count($pl['count']) }}</small></span>{!! $arrow !!}
                                </a>
                            @endforeach
                        </div>
                        @if(! empty($navPlaces))
                            <p class="m-k">All destinations</p>
                            <div class="m-chips">@foreach($navPlaces as $key => $pl)<a href="{{ route('packages.index', ['place' => $key]) }}">{{ $pl['name'] }}</a>@endforeach</div>
                        @endif
                        <a class="m-all" href="{{ route('destinations.index') }}">Map of all destinations {!! $arrow !!}</a>
                    </div></div>
                </div>
                @break

            @case('vacations')
                <div class="sub"><button type="button" class="sub-t" aria-expanded="false">{{ $mi['label'] }}<i class="caret"></i></button>
                    <div class="sub-l"><div>
                        <p class="m-k">Choose how you travel</p>
                        <div class="m-cards">
                            @foreach($tourTypes as $t)
                                @php $tn = $pkgCounts['type'][$t->id] ?? 0; @endphp
                                <a class="m-card" href="{{ route('tours.show', $t->slug) }}">
                                    <img src="{{ $thumb($wayImg[$t->slug] ?? $winter[($loop->index + 2) % count($winter)]) }}" alt="" loading="lazy" width="240" height="160">
                                    <span><b>{{ $t->name }}</b>@if($t->tagline)<em>{{ \Illuminate\Support\Str::limit($t->tagline, 34) }}</em>@endif @if($tn)<small>{{ $count($tn) }}</small>@endif</span>{!! $arrow !!}
                                </a>
                            @endforeach
                        </div>
                        <div class="m-pair">
                            <a class="m-all" href="{{ route('tours.index') }}">Compare them {!! $arrow !!}</a>
                            <a class="m-all" href="{{ route('packages.index') }}">All journeys {!! $arrow !!}</a>
                        </div>
                    </div></div>
                </div>
                @break

            @case('styles')
                <div class="sub"><button type="button" class="sub-t" aria-expanded="false">{{ $mi['label'] }}<i class="caret"></i></button>
                    <div class="sub-l"><div>
                        <ul class="m-rows">
                            @foreach(config('goaustria.travel_styles', []) as $slug => $st)
                                @php $sn = $navStyles[$slug]['count'] ?? 0; @endphp
                                <li><a href="{{ route('packages.index', ['style' => $slug]) }}"><span><b>{{ $st['name'] }}</b><em>{{ $st['text'] }}</em></span>@if($sn)<small>{{ $sn }}</small>@endif</a></li>
                            @endforeach
                        </ul>
                        <a class="m-all" href="{{ route('styles.index') }}">All travel styles {!! $arrow !!}</a>
                    </div></div>
                </div>
                @break

            @case('about')
                <div class="sub"><button type="button" class="sub-t" aria-expanded="false">{{ $mi['label'] }}<i class="caret"></i></button>
                    <div class="sub-l"><div>
                        <ul class="m-rows">
                            <li><a href="{{ route('about') }}"><span><b>About {{ $brand }}</b><em>Who we are and how we plan your trip</em></span></a></li>
                            <li><a href="{{ route('journal.index') }}"><span><b>Journal</b><em>Travel ideas and guides to Austria</em></span></a></li>
                            <li><a href="{{ route('testimonials') }}"><span><b>Guest stories</b><em>What our travellers say</em></span></a></li>
                            @foreach($navPages['about'] ?? [] as $np)<li><a href="{{ route('pages.show', $np->slug) }}"><span><b>{{ $np->title }}</b>@if($np->intro)<em>{{ \Illuminate\Support\Str::limit($np->intro, 48) }}</em>@endif</span></a></li>@endforeach
                        </ul>
                        <p class="m-legal"><a href="{{ route('terms') }}">Terms</a><a href="{{ route('privacy') }}">Privacy</a><a href="{{ route('photo-credits') }}">Photo credits</a></p>
                    </div></div>
                </div>
                @break

            @case('contact')
                <a class="m-link" href="{{ route('contact') }}">{{ $mi['label'] }}</a>
                @break

            @default
                <a class="m-link" href="{{ \App\Support\Menus::href($mi['url']) }}" data-close-menu>{{ $mi['label'] }}</a>
        @endswitch
    @endforeach
    </div>

    <div class="m-cta">
        @if($mh['cta_label'] !== '')
            @if($mh['cta_url'] !== '')<a class="btn" href="{{ \App\Support\Menus::href($mh['cta_url']) }}" data-close-menu>{{ $mh['cta_label'] }} <i aria-hidden="true">&rarr;</i></a>@else<a class="btn" href="{{ route('contact') }}" data-quote data-close-menu>{{ $mh['cta_label'] }} <i aria-hidden="true">&rarr;</i></a>@endif
        @endif
        @if($phoneOk)<a class="m-ph" href="tel:{{ $tel }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg>{{ $phone }}</a>@endif
        <p class="m-note">Free quote · No obligation</p>
    </div>
</nav>

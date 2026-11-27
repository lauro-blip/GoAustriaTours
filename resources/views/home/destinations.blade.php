{{-- Home: destination tiles = the 6 most-visited places we hold a photo of (App\Support\Places, via $navPlaces from pages/home), no regions. --}}
@php
    $tiles = collect($navPlaces)->filter(fn ($pl) => $pl['imageUrl'])->sortByDesc('count')->take(6);
    $pinSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>';
@endphp
<section class="sec{{ ! empty($bg) ? ' '.$bg : '' }}" id="destinations">
    <div class="wrap">
        @include('home._head')
        <div class="dgrid three">
            @foreach($tiles as $key => $pl)
                <article class="dtile rv">
                    <div class="dt-photo">
                        <img src="{{ \App\Support\Places::photo($key, 'md') }}" alt="" loading="lazy">
                        <p class="dt-kick">{{ $pl['count'] }} {{ $pl['count'] === 1 ? 'journey' : 'journeys' }}</p>
                        <div class="dt-title">
                            <h3>{{ $pl['name'] }}</h3>
                            @if($pl['region'] !== $pl['name'])<p>{!! $pinSvg !!} {{ $pl['region'] }}</p>@endif
                        </div>
                    </div>
                    <div class="dt-body">
                        <p class="dt-fact">{{ $pl['fact'] }}</p>
                        <div class="dt-foot">
                            <a class="dt-explore" href="{{ route('packages.index', ['place' => $key]) }}">Explore</a>
                            <a class="btn sm" href="{{ route('contact') }}" data-quote data-interest="{{ $pl['name'] }}">Enquire now</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        @if(filled($d['link_label'] ?? null))<p class="dest-foot"><a class="link" href="{{ route('destinations.index') }}">{{ $tx($d['link_label']) }} &rarr;</a></p>@endif
    </div>
</section>

{{-- Home: photo mosaic (positions g-a .. g-g follow the order). Each tile links to journeys matching the first word of its title. --}}
@php
    $tiles = collect($d['items'] ?? [])->map(fn ($g) => $g + ['img' => \App\Support\Blocks::image($g['image'] ?? null)])->filter(fn ($g) => $g['img'])->values();
    $pos = ['g-a', 'g-b', 'g-c', 'g-d', 'g-e', 'g-f', 'g-g'];
@endphp
@if($tiles->isNotEmpty())
<section class="sec{{ ! empty($bg) ? ' '.$bg : '' }}" id="gallery">
    <div class="wrap">
        @include('home._head')
        <div class="gal">
            @foreach($tiles as $g)
                @php $gq = explode(' ', trim((string) ($g['title'] ?? '')))[0] ?? ''; @endphp
                <a class="gi {{ $pos[$loop->index] ?? '' }} rv" href="{{ $gq !== '' ? route('packages.index', ['q' => $gq]) : route('packages.index') }}">
                    <img src="{{ $g['img']['src'] }}" alt="{{ $g['img']['alt'] ?: trim(($g['title'] ?? '').', '.($g['label'] ?? ''), ', ') }}" loading="lazy">
                    @if(filled($g['label'] ?? null))<span class="gl"{{ $ed('items.'.$loop->index.'.label') }}>{{ $g['label'] }}</span>@endif
                    <div class="gt"><h3{{ $ed('items.'.$loop->index.'.title') }}>{{ $g['title'] ?? '' }}</h3><i aria-hidden="true">&rarr;</i></div>
                </a>
            @endforeach
        </div>
        @if(filled($d['button_label'] ?? null))<p class="center"><a class="btn" href="{{ route('contact') }}" data-quote>{{ $tx($d['button_label']) }} <i aria-hidden="true">&rarr;</i></a></p>@endif
    </div>
</section>
@endif

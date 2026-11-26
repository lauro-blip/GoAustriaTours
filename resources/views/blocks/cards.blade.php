@if(! empty($d['items']))
<section class="sec blk {{ $tone }}">
    <div class="wrap">
        @include('blocks._head', ['intro' => $d['intro'] ?? null])
        <div class="blk-cards">
            @foreach($d['items'] as $c)
                @php $img = \App\Support\Blocks::image($c['image'] ?? null); $link = $c['link'] ?? ''; @endphp
                <article class="blk-card">
                    @if($img)<div class="blk-card-ph"><img src="{{ $img['src'] }}" @if($img['srcset']) srcset="{{ $img['srcset'] }}" sizes="(min-width: 1000px) 33vw, 100vw" @endif alt="{{ $img['alt'] }}" loading="lazy"></div>@endif
                    <div class="blk-card-b">
                        @if(filled($c['title'] ?? null))
                            <h3>
                                @if($link !== '')<a href="{{ $link }}">{{ $c['title'] }}</a>@else{{ $c['title'] }}@endif
                            </h3>
                        @endif
                        @if(filled($c['text'] ?? null))<p>{{ $c['text'] }}</p>@endif
                        @if($link !== '')<span class="plx-more" aria-hidden="true">Discover <i>&rarr;</i></span>@endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@php $img = \App\Support\Blocks::image($d['image'] ?? null); $link = $d['button_link'] ?? ''; @endphp
<section class="sec blk {{ $tone }}"{{ $sx }}>
    <div @class(['wrap', 'blk-it', 'flip' => ($d['side'] ?? 'left') === 'right', 'no-img' => ! $img])>
        @if($img)<figure class="blk-it-ph"><img src="{{ $img['src'] }}" @if($img['srcset']) srcset="{{ $img['srcset'] }}" sizes="(min-width: 1000px) 50vw, 100vw" @endif alt="{{ $img['alt'] }}" loading="lazy"></figure>@endif
        <div class="blk-it-copy">
            @if(filled($d['eyebrow'] ?? null))<p class="pk-k"{{ $ed('eyebrow') }}>{{ $d['eyebrow'] }}</p>@endif
            @if(filled($d['heading'] ?? null))<h2 class="h"{{ $ed('heading') }}>{{ $d['heading'] }}</h2>@endif
            @if(filled($d['body'] ?? null))<div class="prose">{!! $d['body'] !!}</div>@endif
            @if(filled($d['button_label'] ?? null))
                <p class="blk-act">
                    @if($link !== '')
                        <a class="btn" href="{{ $link }}">{{ $d['button_label'] }} <i aria-hidden="true">&rarr;</i></a>
                    @else
                        <a class="btn" href="{{ route('contact') }}" data-quote>{{ $d['button_label'] }} <i aria-hidden="true">&rarr;</i></a>
                    @endif
                </p>
            @endif
        </div>
    </div>
</section>

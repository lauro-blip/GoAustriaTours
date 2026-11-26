{{-- Home: the difference (dark photo section) with real counts only. --}}
@php $bg = \App\Support\Blocks::image($d['image'] ?? null); $link2 = (string) ($d['link'] ?? ''); @endphp
<section class="sec diff">
    @if($bg)<div class="diff-bg" aria-hidden="true"><img src="{{ $bg['src'] }}" alt="" loading="lazy"></div>@endif
    <div class="wrap diff-grid">
        <div class="diff-copy rv">
            @include('home._head', ['hc' => false, 'light' => true])
            @if(! empty($d['points']))
                <ul class="diff-points">@foreach($d['points'] as $pt)<li{{ $ed('points.'.$loop->index.'.text') }}>{{ $tx($pt['text'] ?? '') }}</li>@endforeach</ul>
            @endif
            <div class="diff-cta">
                @if(filled($d['button_label'] ?? null))<a class="btn" href="{{ route('contact') }}" data-quote>{{ $tx($d['button_label']) }} <i aria-hidden="true">&rarr;</i></a>@endif
                @if(filled($d['link_label'] ?? null) && $link2 !== '')<a class="link" href="{{ $link2 }}">{{ $tx($d['link_label']) }}</a>@endif
            </div>
        </div>
        <div class="stats rv">
            @foreach([[$packages->count(), 1], [$destinations->count(), 2], [$tourTypes->count(), 3]] as [$num, $si])
                <div class="stat"><b data-n="{{ $num }}">0</b><div><strong{{ $ed("stat{$si}_label") }}>{{ $tx($d["stat{$si}_label"] ?? '') }}</strong>@if(filled($d["stat{$si}_text"] ?? null))<span{{ $ed("stat{$si}_text") }}>{{ $tx($d["stat{$si}_text"]) }}</span>@endif</div></div>
            @endforeach
        </div>
    </div>
</section>

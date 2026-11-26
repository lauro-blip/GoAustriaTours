{{-- Home: why travel with us (photo + story). --}}
@php $whyImg = \App\Support\Blocks::image($d['image'] ?? null); @endphp
<section class="sec why">
    <div class="wrap why-grid">
        @if($whyImg)
        <div class="why-media rv">
            <figure class="why-img"><img src="{{ $whyImg['src'] }}" @if($whyImg['srcset']) srcset="{{ $whyImg['srcset'] }}" sizes="(min-width: 900px) 45vw, 100vw" @endif alt="{{ $whyImg['alt'] }}" loading="lazy"></figure>
        </div>
        @endif
        <div class="why-copy rv">
            @include('home._head', ['hc' => false])
            @if(filled($d['p1'] ?? null))<p class="why-p">{{ $tx($d['p1']) }}</p>@endif
            @if(filled($d['p2'] ?? null))<p class="why-p">{{ $tx($d['p2']) }}</p>@endif
            @if(filled($d['button_label'] ?? null))<div class="why-cta"><a class="btn" href="{{ route('contact') }}" data-quote>{{ $tx($d['button_label']) }} <i aria-hidden="true">&rarr;</i></a></div>@endif
        </div>
    </div>
</section>

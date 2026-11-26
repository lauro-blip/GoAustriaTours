{{-- Home: full-width photo call to action. --}}
@php $bg = \App\Support\Blocks::image($d['image'] ?? null); @endphp
<section class="sec cta2" id="plan">
    @if($bg)<div class="cta2-bg" aria-hidden="true"><img src="{{ $bg['src'] }}" alt="" loading="lazy"></div>@endif
    <div class="wrap center rv">
        @include('home._head', ['hc' => false, 'light' => true])
        <div class="cta2-btns">
            @if(filled($d['button_label'] ?? null))<a class="btn" href="{{ route('contact') }}" data-quote>{{ $tx($d['button_label']) }} <i aria-hidden="true">&rarr;</i></a>@endif
            @if($phone)<a class="cta2-call" href="tel:{{ $tel }}">Or call {{ $phone }}</a>@endif
        </div>
    </div>
</section>

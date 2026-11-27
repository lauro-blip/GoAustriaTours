{{-- Package card for the home page. Var: $f = {p: Package, image, places, price}. --}}
@php $p = $f->p; @endphp
<article class="pc">
    <a class="ph" href="{{ route('packages.show', $p) }}" tabindex="-1" aria-hidden="true">
        <img src="{{ $f->image }}" alt="" loading="lazy" width="768" height="576">
        @if($p->tourType)<span class="tag">{{ $p->tourType->name }}</span>@endif
        @if($p->nights)<span class="dur"><b>{{ $p->nights + 1 }}</b> days <i>&middot;</i> <b>{{ $p->nights }}</b> nights</span>@endif
    </a>
    <div class="pb">
        @if($p->nights)<p class="mdur"><b>{{ $p->nights + 1 }}</b> days &middot; <b>{{ $p->nights }}</b> nights</p>@endif
        <h3><a href="{{ route('packages.show', $p) }}">{{ $p->title }}</a></h3>
        @if($f->places->isNotEmpty())
            <p class="route"><span>Route</span>{{ $f->places->take(4)->implode(' › ') }}</p>
        @endif
        @if($p->summary)<p class="sum">{{ $p->summary }}</p>@endif
        <div class="pfoot">
            <p class="price"><small>From</small><b>{{ $f->price ?? 'Price on request' }}</b>@if($f->price)<span>per person</span>@endif</p>
            <div class="btns">
                <a class="more" href="{{ route('packages.show', $p) }}">Details <i aria-hidden="true">&rarr;</i></a>
                <a class="btn sm" href="{{ route('packages.show', $p) }}#quote" data-quote data-package-id="{{ $p->id }}" data-package="{{ $p->title }}">Enquire</a>
            </div>
        </div>
    </div>
</article>

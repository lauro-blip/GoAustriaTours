{{-- Home: ways to travel (tour types). --}}
<section class="sec{{ ! empty($bg) ? ' '.$bg : '' }}" id="ways">
    <div class="wrap">
        @include('home._head', ['oneline' => true])
        <div class="ways">
            @foreach($tourTypes->take(3) as $t)
                @php $tn = $pkgs->filter(fn ($f) => $f->p->tourType?->id === $t->id)->count(); @endphp
                <a class="way rv" href="{{ route('tours.show', $t->slug) }}">
                    <img src="{{ $w($wayImg[$t->slug] ?? $winter[($loop->index + 3) % count($winter)], '-768') }}" alt="" loading="lazy">
                    @if($tn)<span class="wcount">{{ $tn }} {{ $tn === 1 ? 'journey' : 'journeys' }}</span>@endif
                    <div class="wbody">
                        <h3>{{ $t->name }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($t->tagline ?: $t->summary, 80) }}</p>
                        <span class="wgo">Explore {{ $t->name }} <i aria-hidden="true">&rarr;</i></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

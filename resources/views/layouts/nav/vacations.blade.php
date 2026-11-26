{{-- Austria Vacations mega menu (dropdown panel of a built-in header item; data from the layouts.site composer). --}}
<div class="mega">
    <div class="mega-in tt">
        <div>
            <h5>Choose how you travel</h5>
            <div class="mega-cards c3">
                @foreach($tourTypes as $t)
                    @php $tn = $pkgCounts['type'][$t->id] ?? 0; @endphp
                    <a href="{{ route('tours.show', $t->slug) }}"><img src="{{ $w($wayImg[$t->slug] ?? $winter[($loop->index + 2) % count($winter)], '-768') }}" alt="" loading="lazy"><span><b>{{ $t->name }}</b><em>{{ \Illuminate\Support\Str::limit((string) $t->tagline, 40) }}</em>@if($tn)<small>{{ $tn }} {{ $tn === 1 ? 'journey' : 'journeys' }}</small>@endif</span></a>
                @endforeach
            </div>
            <a class="more" href="{{ route('tours.index') }}">Compare the ways to travel &rarr;</a>
        </div>
        <aside class="mega-help" aria-label="Personal planning">
            <p class="mp-k">Personal planning</p>
            <h5>Not sure which way to travel?</h5>
            <p class="mh-lead">A specialist will suggest the best fit, free and with no obligation.</p>
            <a class="btn sm" href="{{ route('contact') }}" data-quote>Plan my journey <i aria-hidden="true">&rarr;</i></a>
        </aside>
    </div>
</div>

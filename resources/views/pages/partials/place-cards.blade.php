{{-- Verified place cards. Var: $places (array from App\Support\Places). Photos only where we hold a photo of the place. --}}
@if(! empty($places))
<div class="plc">
    @foreach($places as $pl)
        <article class="plc-i {{ $pl['imageUrl'] ? 'has-img' : '' }}">
            @if($pl['imageUrl'])<a class="plc-img" href="{{ $pl['searchUrl'] }}" tabindex="-1" aria-hidden="true"><img src="{{ $pl['imageUrl'] }}" alt="" loading="lazy"></a>@endif
            <div class="plc-b">
                <p class="plc-k">{{ $pl['region'] }}</p>
                <h3><a href="{{ $pl['searchUrl'] }}">{{ $pl['name'] }}</a></h3>
                <p class="plc-f">{{ $pl['fact'] }}</p>
                <p class="plc-l"><a href="{{ $pl['searchUrl'] }}">Journeys here</a><a href="{{ $pl['mapUrl'] }}" target="_blank" rel="noopener">View on map</a></p>
            </div>
        </article>
    @endforeach
</div>
@endif

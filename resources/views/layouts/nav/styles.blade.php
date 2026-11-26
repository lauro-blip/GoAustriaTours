{{-- Travel Style mega menu (dropdown panel of a built-in header item; data from the layouts.site composer). --}}
<div class="mega">
    <div class="mega-in one">
        <div>
            <h5>Travel style</h5>
            <div class="mega-cards c3">
                @foreach(config('goaustria.travel_styles', []) as $slug => $st)
                    <a class="txt" href="{{ route('packages.index', ['style' => $slug]) }}"><b>{{ $st['name'] }}</b><em>{{ $st['text'] }}</em><u>@if(($navStyles[$slug]['count'] ?? 0) > 0){{ $navStyles[$slug]['count'] }} {{ $navStyles[$slug]['count'] === 1 ? 'journey' : 'journeys' }}@else Discover @endif <i aria-hidden="true">&rarr;</i></u></a>
                @endforeach
            </div>
            <a class="more" href="{{ route('styles.index') }}">Explore all travel styles &rarr;</a>
        </div>
    </div>
</div>

{{-- Travel Style mega menu (dropdown panel of a built-in header item; data from the layouts.site composer).
     Each style = a row with a small photo (`{image}-240.webp`), name, one-line text and journey count; promo panel on the right. --}}
<div class="mega">
    <div class="mega-in sty">
        <div>
            <h5>Travel by what you love</h5>
            <ul class="sty-list">
                @foreach(config('goaustria.travel_styles', []) as $slug => $st)
                    @php $sc = $navStyles[$slug]['count'] ?? 0; @endphp
                    <li><a href="{{ route('packages.index', ['style' => $slug]) }}">
                        <span class="sty-img"><img src="{{ asset('images/'.$st['image'].'-240.webp') }}" alt="" loading="lazy" width="240" height="160"></span>
                        <span class="sty-txt"><b>{{ $st['name'] }}</b><em>{{ $st['text'] }}</em><small>@if($sc > 0){{ $sc }} {{ $sc === 1 ? 'journey' : 'journeys' }}@else Discover @endif <i aria-hidden="true">&rarr;</i></small></span>
                    </a></li>
                @endforeach
            </ul>
        </div>
        <aside class="sty-promo">
            <p class="mp-k">Personal planning</p>
            <h5>Not sure which style suits you?</h5>
            <p>Mix as many as you like. Tell us what you enjoy and a specialist will shape one journey around it.</p>
            <a class="btn sm" href="{{ route('contact') }}" data-quote data-interest="Travel style advice">Help me choose <i aria-hidden="true">&rarr;</i></a>
            <a class="sty-all" href="{{ route('styles.index') }}">Explore all travel styles <i aria-hidden="true">&rarr;</i></a>
        </aside>
    </div>
</div>

{{-- Home: tours grid (destination chips + 6-per-page pager in home.js). --}}
<section class="sec tint" id="tours" data-tours>
    <div class="wrap">
        <div class="tours-head rv">
            @include('home._head', ['hc' => ''])
            <div class="tours-filter">
                <label class="tsel"><span class="sr-only">Filter by destination</span>
                    <select data-dest-select>
                        <option value="">All packages</option>
                        @foreach($destinations as $dst)<option value="{{ $dst->slug }}">{{ $dst->name }}</option>@endforeach
                    </select>
                </label>
                <div class="chips" role="group" aria-label="Filter by destination">
                    <button type="button" class="chip on" data-dest="">All packages</button>
                    @foreach($destinations as $dst)<button type="button" class="chip" data-dest="{{ $dst->slug }}">{{ $dst->name }}</button>@endforeach
                </div>
                <p class="count" data-count aria-live="polite">Showing 1&ndash;{{ min(6, $featured->count()) }} of {{ $featured->count() }} tours</p>
            </div>
        </div>
        <div class="grid" data-grid>
            @foreach($featured as $f)
                <div class="cell" data-dest="{{ $f->dest }}">@include('pages.partials.package-card', ['f' => $f])</div>
            @endforeach
        </div>
        <nav class="pager" data-pager aria-label="Pagination"></nav>
        <div class="tours-cta">
            @if(filled($d['button_label'] ?? null))<a class="btn" href="{{ route('contact') }}" data-quote>{{ $tx($d['button_label']) }} <i aria-hidden="true">&rarr;</i></a>@endif
            @if(filled($d['link_label'] ?? null))<a class="link" href="{{ route('packages.index') }}">{{ $tx($d['link_label']) }}</a>@endif
        </div>
    </div>
</section>

{{-- Home: featured journeys ("moments"). --}}
@if($pkgs->isNotEmpty())
<section class="sec{{ ! empty($bg) ? ' '.$bg : '' }}" id="moments">
    <div class="wrap">
        @include('home._head', ['oneline' => true])
        <div class="mom">
            @foreach($pkgs->take(6) as $f)
                <article class="mc rv">
                    <img src="{{ $w($winter[($loop->index + 4) % count($winter)], '-768') }}" alt="" loading="lazy">
                    @if($f->p->tourType)<span class="mt">{{ $f->p->tourType->name }}</span>@endif
                    <div class="mb">
                        @if($f->places->isNotEmpty())<p class="mk">{{ $f->places->take(2)->implode(' · ') }}</p>@endif
                        <h3><a href="{{ route('packages.show', $f->p) }}">{{ $f->p->title }}</a></h3>
                        <p class="mm">@if($f->p->nights){{ $f->p->nights }} nights<span aria-hidden="true"> &middot; </span>@endif{{ $f->price ? 'From '.$f->price : 'Price on request' }}</p>
                        <div class="mact">
                            <a class="mview" href="{{ route('packages.show', $f->p) }}">View journey</a>
                            <a class="btn sm" href="{{ route('packages.show', $f->p) }}#quote" data-quote data-package-id="{{ $f->p->id }}" data-package="{{ $f->p->title }}">Enquire</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

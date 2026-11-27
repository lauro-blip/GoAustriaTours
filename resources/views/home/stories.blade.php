{{-- Home: guest stories (Admin > Testimonials). --}}
@if($testimonials->isNotEmpty())
<section class="sec{{ ! empty($bg) ? ' '.$bg : '' }}" id="stories">
    <div class="wrap">
        @include('home._head', ['oneline' => true])
        <div class="reviews tcards" data-reviews>
            <div class="rtrack">
                @foreach($testimonials as $t)
                    @php $ini = collect(preg_split('/\s+/', trim($t->name)))->filter()->take(2)->map(fn ($x) => mb_strtoupper(mb_substr($x, 0, 1)))->implode(''); @endphp
                    <figure class="rev">
                        <span class="qi" aria-hidden="true"><svg viewBox="0 0 48 36" fill="currentColor"><path d="M0 36V19C0 8 6 1.5 17 0l1.6 5C13 6.6 10.5 10 10.5 15H19v21H0zm27 0V19C27 8 33 1.5 44 0l1.600 5C40 6.600 37.500 10 37.500 15H46v21H27z"/></svg></span>
                        <blockquote>{{ $t->quote }}</blockquote>
                        <figcaption><span class="av" aria-hidden="true">{{ $ini }}</span><span class="who"><b>{{ $t->name }}</b>@if($t->location)<small>{{ $t->location }}</small>@endif</span></figcaption>
                    </figure>
                @endforeach
            </div>
            @if($testimonials->count() > 3)
                <div class="rnav"><button type="button" data-prev aria-label="Previous story">&larr;</button><button type="button" data-next aria-label="Next story">&rarr;</button></div>
            @endif
        </div>
    </div>
</section>
@endif

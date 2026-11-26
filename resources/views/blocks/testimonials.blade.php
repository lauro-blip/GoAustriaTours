@php $stories = \App\Models\Testimonial::where('is_active', true)->orderBy('sort_order')->take((int) ($d['limit'] ?? 3))->get(); @endphp
@if($stories->isNotEmpty())
<section class="sec blk {{ $tone }}">
    <div class="wrap">
        @include('blocks._head')
        <div class="tgrid tcards">
            @foreach($stories as $t)
                @php $ini = collect(preg_split('/\s+/', trim($t->name)))->filter()->take(2)->map(fn ($x) => mb_strtoupper(mb_substr($x, 0, 1)))->implode(''); @endphp
                <figure class="rev">
                    <blockquote>{{ $t->quote }}</blockquote>
                    <figcaption>
                        <span class="av" aria-hidden="true">{{ $ini }}</span>
                        <span class="who"><b>{{ $t->name }}</b>@if($t->location)<small>{{ $t->location }}</small>@endif</span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

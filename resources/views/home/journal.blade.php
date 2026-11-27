{{-- Home: the three latest Journal posts. --}}
@if($posts->isNotEmpty())
<section class="sec{{ ! empty($bg) ? ' '.$bg : '' }}" id="journal">
    <div class="wrap">
        @include('home._head', ['oneline' => true])
        <div class="jr">
            @foreach($posts as $post)
                <article class="jc rv">
                    <a class="jimg" href="{{ route('journal.show', $post) }}" tabindex="-1" aria-hidden="true"><img src="{{ $post->imageUrl() }}" alt="{{ $post->image_alt }}" loading="lazy" width="800" height="600"></a>
                    <div class="jb">
                        <p class="jmeta">@if($post->category)<span>{{ $post->category }}</span><i aria-hidden="true">&middot;</i>@endif<time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('M j, Y') }}</time><i aria-hidden="true">&middot;</i><span>{{ $post->readingMinutes() }} min read</span></p>
                        <h3><a href="{{ route('journal.show', $post) }}">{{ $post->title }}</a></h3>
                        <p class="jx">{{ $post->excerpt }}</p>
                        <a class="jmore" href="{{ route('journal.show', $post) }}">Read article <i aria-hidden="true">&rarr;</i></a>
                    </div>
                </article>
            @endforeach
        </div>
        @if(filled($d['button_label'] ?? null))<p class="center"><a class="btn" href="{{ route('journal.index') }}">{{ $tx($d['button_label']) }} <i aria-hidden="true">&rarr;</i></a></p>@endif
    </div>
</section>
@endif

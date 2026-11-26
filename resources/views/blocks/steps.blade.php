@if(! empty($d['items']))
<section class="sec blk {{ $tone }}"{{ $sx }}>
    <div class="wrap">
        @include('blocks._head')
        <div class="steps">
            @foreach($d['items'] as $s)
                <div>
                    <b>{{ $loop->iteration }}</b>
                    @if(filled($s['title'] ?? null))<h3{{ $ed('items.'.$loop->index.'.title') }}>{{ $s['title'] }}</h3>@endif
                    @if(filled($s['text'] ?? null))<p{{ $ed('items.'.$loop->index.'.text') }}>{{ $s['text'] }}</p>@endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

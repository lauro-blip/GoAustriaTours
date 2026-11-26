@if(! empty($d['items']))
<section class="sec blk {{ $tone }}">
    <div class="wrap">
        @include('blocks._head')
        <div class="steps">
            @foreach($d['items'] as $s)
                <div>
                    <b>{{ $loop->iteration }}</b>
                    @if(filled($s['title'] ?? null))<h3>{{ $s['title'] }}</h3>@endif
                    @if(filled($s['text'] ?? null))<p>{{ $s['text'] }}</p>@endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

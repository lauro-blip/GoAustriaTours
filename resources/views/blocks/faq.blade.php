@if(! empty($d['items']))
<section class="sec blk {{ $tone }}"{{ $sx }}>
    <div class="wrap narrow">
        @include('blocks._head')
        <div class="faq">
            @foreach($d['items'] as $f)<details><summary{{ $ed('items.'.$loop->index.'.question') }}>{{ $f['question'] }}</summary><p{{ $ed('items.'.$loop->index.'.answer') }}>{{ $f['answer'] }}</p></details>@endforeach
        </div>
    </div>
</section>
@endif

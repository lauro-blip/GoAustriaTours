@if(! empty($d['items']))
<section class="sec blk {{ $tone }}">
    <div class="wrap narrow">
        @include('blocks._head')
        <div class="faq">
            @foreach($d['items'] as $f)<details><summary>{{ $f['question'] }}</summary><p>{{ $f['answer'] }}</p></details>@endforeach
        </div>
    </div>
</section>
@endif

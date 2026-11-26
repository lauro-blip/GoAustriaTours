<section class="sec blk blk-text {{ $tone }}">
    <div @class(['wrap', 'narrow', 'blk-center' => ($d['align'] ?? '') === 'center'])>
        @include('blocks._head')
        @if(filled($d['body'] ?? null))<div class="prose">{!! $d['body'] !!}</div>@endif
    </div>
</section>

{{-- Columns section: a heading, then 1-4 columns of elements (resources/views/blocks/el/*). Layout and alignment are fixed classes (inner.css "Columns"). --}}
@php
    $nCols = \App\Support\Blocks::LAYOUT_COLS[$d['layout'] ?? '2'] ?? 2;
    $cols = array_slice(array_pad((array) ($d['cols'] ?? []), $nCols, ['elements' => []]), 0, $nCols);
@endphp
<section class="sec blk blk-cols-sec {{ $tone }}"{{ $sx }}>
    <div class="wrap">
        @include('blocks._head')
        <div class="blk-cols lay-{{ $d['layout'] ?? '2' }} {{ ($d['valign'] ?? 'top') === 'center' ? 'va-c' : '' }}">
            @foreach($cols as $ci => $col)
                <div class="blk-col">
                    @foreach($col['elements'] ?? [] as $ei => $el)
                        @if(view()->exists('blocks.el.'.$el['type']))
                            @include('blocks.el.'.$el['type'], ['e' => $el['data'] + \App\Support\Blocks::blankElement($el['type'])['data'], 'ep' => 'cols.'.$ci.'.elements.'.$ei.'.data.', 'secHasHead' => filled($d['heading'] ?? null)])
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Renders page builder blocks (App\Support\Blocks). Var: $blocks. Hidden blocks are skipped. Each block view gets $d (its data, with defaults
     filled in for fields added later), $i (position) and $tone (section class). Home page sections ("home_*") render from resources/views/home. --}}
@foreach($blocks ?? [] as $b)
    @continue(! empty($b['hidden']))
    @php
        $bType = (string) ($b['type'] ?? '');
        $bView = str_starts_with($bType, 'home_') ? 'home.'.substr($bType, 5) : 'blocks.'.$bType;
    @endphp
    @if(isset(\App\Support\Blocks::all()[$bType]) && view()->exists($bView))
        @include($bView, ['d' => ($b['data'] ?? []) + \App\Support\Blocks::blank($bType)['data'], 'i' => $loop->index, 'tone' => ['tint' => 'tint', 'dark' => 'blk-dark'][$b['data']['tone'] ?? 'light'] ?? ''])
    @endif
@endforeach

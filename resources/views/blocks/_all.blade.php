{{-- Renders page builder blocks (App\Support\Blocks). Var: $blocks. Hidden blocks are skipped. Each block view gets $d (its data, with defaults
     filled in for fields added later), $i (position), $tone (section classes from the Style options: background, spacing, width,
     heading alignment, device visibility), $sx (inline --blk-bg for a photo background) and $ed (inline-editing attribute helper).
     Home page sections ("home_*") render from resources/views/home and keep their own design.
     In the admin live preview ($editing) each block is wrapped in a display:contents marker and text fields get data-edit attributes;
     public pages get neither. --}}
@php $editing = $editing ?? false; @endphp
@foreach($blocks ?? [] as $b)
    @continue(! empty($b['hidden']))
    @php
        $bType = (string) ($b['type'] ?? '');
        $bKnown = isset(\App\Support\Blocks::all()[$bType]);
        $bView = str_starts_with($bType, 'home_') ? 'home.'.substr($bType, 5) : 'blocks.'.$bType;
        $bData = ($b['data'] ?? []) + ($bKnown ? \App\Support\Blocks::blank($bType)['data'] : []);
        $bStyle = str_starts_with($bType, 'home_') ? '' : \App\Support\Blocks::styleClasses($bData);
        $bBg = ($bData['tone'] ?? '') === 'photo' ? \App\Support\Blocks::image($bData['bg_image'] ?? null) : null;
        $bSx = $bBg ? new \Illuminate\Support\HtmlString(' style="--blk-bg:url(&quot;'.e($bBg['src']).'&quot;)"') : '';
        $ed = fn (string $field) => $editing ? new \Illuminate\Support\HtmlString(' data-edit="'.e($field).'"') : '';
    @endphp
    @if($bKnown && view()->exists($bView))
        @if($editing)<div data-blk="{{ $loop->index }}" data-blk-type="{{ $bType }}" style="display:contents">@endif
        @include($bView, ['d' => $bData, 'i' => $loop->index, 'tone' => $bStyle, 'sx' => $bSx, 'ed' => $ed])
        @if($editing)</div>@endif
    @endif
@endforeach

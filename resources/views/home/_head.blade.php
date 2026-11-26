{{-- Home section heading. Vars: $d (eyebrow, heading, heading_em, lede), optional $light, $oneline, $hc (wrapper class; false = no wrapper). --}}
@php $light = $light ?? false; $hc = $hc ?? 'head rv'; @endphp
@if($hc !== false)<div class="{{ $hc }}">@endif
    @if(filled($d['eyebrow'] ?? null))<p class="eyebrow {{ $light ? 'light' : 'dark' }}">{{ $tx($d['eyebrow']) }}</p>@endif
    @if(filled($d['heading'] ?? null) || filled($d['heading_em'] ?? null))
        <h2 @class(['h', 'light' => $light, 'oneline' => $oneline ?? false])>{{ $tx($d['heading'] ?? '') }} @if(filled($d['heading_em'] ?? null))<em>{{ $tx($d['heading_em']) }}</em>@endif</h2>
    @endif
    @if(filled($d['lede'] ?? null))<p class="lede {{ $light ? 'light' : '' }}">{{ $tx($d['lede']) }}</p>@endif
@if($hc !== false)</div>@endif

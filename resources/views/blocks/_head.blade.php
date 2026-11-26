{{-- Shared block heading (small label + heading [+ intro]). Vars: $d, optional $intro. --}}
@if(filled($d['eyebrow'] ?? null) || filled($d['heading'] ?? null) || filled($intro ?? null))
<div class="head blk-head">
    @if(filled($d['eyebrow'] ?? null))<p class="eyebrow dark">{{ $d['eyebrow'] }}</p>@endif
    @if(filled($d['heading'] ?? null))<h2 class="h">{{ $d['heading'] }}</h2>@endif
    @if(filled($intro ?? null))<p class="blk-intro">{{ $intro }}</p>@endif
</div>
@endif

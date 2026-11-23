{{-- Inner-page banner. Vars: $bTitle, $bEyebrow (opt), $bLead (opt), $bImage (opt URL), $bAlt (opt), $bCrumbs (opt: [[label, url|null], ...]), $bActions (opt HTML), $bClass (opt) --}}
<section class="pbanner {{ $bClass ?? '' }}">
    @if(! empty($bImage))<div class="pbanner-bg" aria-hidden="true"><img src="{{ $bImage }}" alt="" fetchpriority="high"></div>@endif
    <div class="wrap pbanner-in">
        @if(! empty($bCrumbs))
            <nav class="crumbs" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a>@foreach($bCrumbs as [$cl, $cu])<span aria-hidden="true">/</span>@if($cu)<a href="{{ $cu }}">{{ $cl }}</a>@else<span class="cur">{{ $cl }}</span>@endif @endforeach</nav>
        @endif
        @if(! empty($bEyebrow))<p class="pbanner-eyebrow">{{ $bEyebrow }}</p>@endif
        <h1>{{ $bTitle }}</h1>
        @if(! empty($bLead))<p class="pbanner-lead">{{ $bLead }}</p>@endif
        @if(! empty($bActions)){!! $bActions !!}@endif
    </div>
</section>

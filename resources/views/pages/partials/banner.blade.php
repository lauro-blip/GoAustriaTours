{{-- Inner-page banner. Vars: $bTitle, $bEyebrow (opt), $bLead (opt), $bImage (opt URL), $bSrcset (opt srcset for $bImage), $bAlt (opt), $bCrumbs (opt: [[label, url|null], ...]), $bActions (opt HTML), $bAside (opt HTML, desktop side card), $bClass (opt) --}}
<section class="pbanner {{ $bClass ?? '' }}">
    @if(! empty($bImage))@push('preload')<link rel="preload" as="image" href="{{ $bImage }}" @if(! empty($bSrcset)) imagesrcset="{{ $bSrcset }}" imagesizes="100vw" @endif fetchpriority="high">@endpush<div class="pbanner-bg" aria-hidden="true"><img src="{{ $bImage }}" @if(! empty($bSrcset)) srcset="{{ $bSrcset }}" sizes="100vw" @endif alt="" fetchpriority="high"></div>@endif
    <div class="wrap pbanner-in @if(! empty($bAside)) has-aside @endif">
        <div class="pbanner-main">
        @if(! empty($bCrumbs))
            <nav class="crumbs" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a>@foreach($bCrumbs as [$cl, $cu])<span aria-hidden="true">/</span>@if($cu)<a href="{{ $cu }}">{{ $cl }}</a>@else<span class="cur">{{ $cl }}</span>@endif @endforeach</nav>
        @endif
        @if(! empty($bEyebrow))<p class="pbanner-eyebrow">{{ $bEyebrow }}</p>@endif
        <h1>{{ $bTitle }}</h1>
        @if(! empty($bLead))<p class="pbanner-lead">{{ $bLead }}</p>@endif
        @if(! empty($bActions)){!! $bActions !!}@endif
        </div>
        @if(! empty($bAside))<aside class="pbanner-aside">{!! $bAside !!}</aside>@endif
    </div>
</section>

{{-- Desktop summary card shown inside the package banner (not a form): price, key facts and the quote button. Vars: $package, $site. --}}
@php
    $hcPrice = $package->price_from ? '$'.number_format($package->price_from) : null;
    $hcPhone = trim((string) ($site['phone'] ?? ''));
    $hcTel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $hcPhone));
@endphp
<div class="pcard">
    <p class="pcard-k">{{ $hcPrice ? 'Starting from' : 'Price' }}</p>
    <p class="pcard-price">{{ $hcPrice ?: 'On request' }}@if($hcPrice)<small> per person</small>@endif</p>
    <ul class="pcard-facts">
        <li><span>Duration</span><b>{{ $package->nights + 1 }} days &middot; {{ $package->nights }} nights</b></li>
        @if($package->tourType)<li><span>Style</span><b>{{ $package->tourType->name }}</b></li>@endif
        @if($package->destination)<li><span>Region</span><b>{{ $package->destination->name }}</b></li>@endif
    </ul>
    <a class="btn" href="{{ route('contact') }}" data-quote data-package-id="{{ $package->id }}" data-package="{{ $package->title }}">Get a free quote <i aria-hidden="true">&rarr;</i></a>
    @if($hcPhone !== '')<a class="pcard-call" href="tel:{{ $hcTel }}">or call {{ $hcPhone }}</a>@endif
    <p class="pcard-note">Free, with no obligation. A local specialist replies personally.</p>
</div>

{{-- Mid-page call to action (no form: it opens the quote popup). Vars: $csTitle, $csText, $csPackage (opt), $csInterest (opt), $csButton (opt). --}}
@php
    $csPhone = trim((string) ($site['phone'] ?? ''));
    $csTel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $csPhone));
@endphp
<section class="cstrip">
    <div class="wrap cstrip-in">
        <div>
            <h2>{{ $csTitle }}</h2>
            <p>{{ $csText }}</p>
        </div>
        <div class="cstrip-act">
            <a class="btn" href="{{ route('contact') }}" data-quote @if(! empty($csPackage)) data-package-id="{{ $csPackage->id }}" data-package="{{ $csPackage->title }}" @endif @if(! empty($csInterest)) data-interest="{{ $csInterest }}" @endif>{{ $csButton ?? 'Get a free quote' }} <i aria-hidden="true">&rarr;</i></a>
            @if($csPhone !== '')<a class="cstrip-ph" href="tel:{{ $csTel }}">or call {{ $csPhone }}</a>@endif
        </div>
    </div>
</section>

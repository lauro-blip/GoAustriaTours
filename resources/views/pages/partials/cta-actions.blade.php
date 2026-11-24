{{-- Banner buttons: primary quote button (opens the popup) + call link + trust line. Vars: $caPackage (opt), $caInterest (opt), $caLabel (opt). --}}
@php
    $caPhone = trim((string) ($site['phone'] ?? ''));
    $caTel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $caPhone));
@endphp
<div class="pbanner-actions">
    <a class="btn" href="{{ route('contact') }}" data-quote @if(! empty($caPackage)) data-package-id="{{ $caPackage->id }}" data-package="{{ $caPackage->title }}" @endif @if(! empty($caInterest)) data-interest="{{ $caInterest }}" @endif>{{ $caLabel ?? 'Get a free quote' }} <i aria-hidden="true">&rarr;</i></a>
    @if($caPhone !== '')<a class="btn ghost" href="tel:{{ $caTel }}">Call {{ $caPhone }}</a>@endif
</div>
<ul class="pbanner-trust"><li>Free quote</li><li>No obligation</li><li>Local Austria specialists</li></ul>

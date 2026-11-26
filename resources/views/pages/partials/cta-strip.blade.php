{{-- Mid-page call to action (no form: it opens the quote popup).
     Vars: $csTitle, $csText, $csPackage (opt), $csInterest (opt), $csButton (opt), $csEyebrow (opt), $csImage (opt, a winter photo name). --}}
@php
    $csPhone = trim((string) ($site['phone'] ?? ''));
    $csTel = preg_replace('/[^\d+]/', '', (string) (($site['phone_tel'] ?? '') ?: $csPhone));
@endphp
<section class="cstrip">
    <div class="cstrip-bg" aria-hidden="true"><img src="{{ asset('images/winter/'.($csImage ?? 'hero-montafon').'-768.webp') }}" alt="" loading="lazy" width="768" height="432"></div>
    <div class="wrap cstrip-in">
        <div class="cstrip-copy">
            <p class="cstrip-k">{{ $csEyebrow ?? 'Personal planning' }}</p>
            <h2>{{ $csTitle }}</h2>
            @if(filled($csText ?? null))<p class="cstrip-t">{{ $csText }}</p>@endif
            <ul class="cstrip-pts">
                <li>Free quote</li>
                <li>No obligation</li>
                <li>Planned around your dates</li>
            </ul>
        </div>
        <div class="cstrip-act">
            <a class="btn" href="{{ route('contact') }}" data-quote @if(! empty($csPackage)) data-package-id="{{ $csPackage->id }}" data-package="{{ $csPackage->title }}" @endif @if(! empty($csInterest)) data-interest="{{ $csInterest }}" @endif>{{ $csButton ?? 'Get a free quote' }} <i aria-hidden="true">&rarr;</i></a>
            @if($csPhone !== '')
                <a class="cstrip-ph" href="tel:{{ $csTel }}">
                    <span class="cstrip-phi" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg></span>
                    <span><small>Prefer to talk?</small><b>{{ $csPhone }}</b></span>
                </a>
            @endif
        </div>
    </div>
</section>

{{-- Home: trust strip. Built only from Admin > Trust & tracking; blank or "[CONFIRM" values never show. --}}
@php
    $marks = collect(range(1, 4))->map(fn ($n) => [
        'label' => trim((string) ($site["trust_mark_{$n}_label"] ?? '')),
        'logo' => trim((string) ($site["trust_mark_{$n}_logo"] ?? '')),
        'url' => trim((string) ($site["trust_mark_{$n}_url"] ?? '')),
    ])->filter(fn ($m) => $m['label'] !== '' || $m['logo'] !== '')->values();
    $established = trim((string) ($site['established_line'] ?? ''));
    $reviewText = trim((string) ($site['review_summary'] ?? ''));
    $reviewUrl = trim((string) ($site['review_url'] ?? ''));
@endphp
@if($marks->isNotEmpty() || $established !== '' || $reviewText !== '')
<section class="trust" aria-label="Why you can trust us">
    <div class="wrap trust-in">
        @if($established !== '')<p class="trust-est">{{ $established }}</p>@endif
        @if($marks->isNotEmpty())
            <ul class="trust-marks" aria-label="Memberships and protection">
                @foreach($marks as $m)
                    <li>
                        @if($m['url'] !== '')<a href="{{ $m['url'] }}" target="_blank" rel="noopener">@endif
                        @if($m['logo'] !== '')<img src="{{ asset('storage/'.$m['logo']) }}" alt="{{ $m['label'] ?: 'Membership logo' }}" loading="lazy">@else<span>{{ $m['label'] }}</span>@endif
                        @if($m['url'] !== '')</a>@endif
                    </li>
                @endforeach
            </ul>
        @endif
        @if($reviewText !== '')<p class="trust-rev">@if($reviewUrl !== '')<a href="{{ $reviewUrl }}" target="_blank" rel="noopener">{{ $reviewText }}</a>@else{{ $reviewText }}@endif</p>@endif
    </div>
</section>
@endif

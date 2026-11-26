@if(filled($d['heading'] ?? null))
    @include('pages.partials.cta-strip', [
        'csTitle' => $d['heading'],
        'csText' => $d['text'] ?? '',
        'csButton' => filled($d['button_label'] ?? null) ? $d['button_label'] : 'Get a free quote',
        'csInterest' => filled($d['interest'] ?? null) ? $d['interest'] : null,
    ])
@endif

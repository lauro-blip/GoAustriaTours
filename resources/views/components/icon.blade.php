@props(['name'])
@php
    // Simple 24px stroke icons. Add new keys here; the quiz editor uses these names.
    $paths = [
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
        'chevron' => '<path d="m6 9 6 6 6-6"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'lock' => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
        'check' => '<path d="m5 12 5 5 9-10"/>',
        'cross' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'question' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.7.4-1 .9-1 1.7M12 17h.01"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'coin' => '<circle cx="12" cy="12" r="9"/><path d="M14.5 9.5c-.5-1-1.5-1.5-2.5-1.5-1.4 0-2.5.8-2.5 2s1 1.7 2.5 2 2.5.8 2.5 2-1.1 2-2.5 2c-1 0-2-.5-2.5-1.5M12 6v2m0 8v2"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/>',
        'leaf' => '<path d="M5 19c0-9 5-14 15-14 0 10-5 15-14 15"/><path d="M5 19c3-5 6-8 10-10"/>',
        'flower' => '<circle cx="12" cy="12" r="2.5"/><path d="M12 9.5C12 6 13 4 12 3c-1 1-0 3-0 6.5M12 14.5c0 3.5 1 5.500 0 6.5-1-1 0-3 0-6.5M9.500 12C6 12 4 11 3 12c1 1 3 0 6.500 0M14.500 12c3.500 0 5.500 1 6.500 0-1-1-3 0-6.500 0"/>',
        'snow' => '<path d="M12 2v20M3.500 7l17 10M3.500 17l17-10M9 4l3 2 3-2M9 20l3-2 3 2"/>',
        'bus' => '<rect x="4" y="4" width="16" height="13" rx="2"/><path d="M4 12h16M7 20v-3M17 20v-3M8 8h.01M16 8h.01"/>',
        'car' => '<path d="M5 16V12l2-5h10l2 5v4M3 16h18M7 16v2M17 16v2M7.500 12.500h.01M16.500 12.500h.01"/>',
        'guide' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'city' => '<path d="M4 21V9l5-3v15M9 21V4l6 3v14M15 21v-9l5 2v7M3 21h18"/>',
        'mountain' => '<path d="m3 20 6-11 4 7 3-5 5 9z"/>',
        'shield' => '<path d="M12 3 5 6v6c0 4.500 3 7.500 7 9 4-1.500 7-4.500 7-9V6z"/><path d="m9 12 2 2 4-4"/>',
        'star' => '<path d="m12 3 2.700 5.600 6.100.9-4.400 4.300 1 6.100L12 17l-5.400 2.900 1-6.100L3.200 9.500l6.100-.9z"/>',
        'pin' => '<path d="M12 21s7-6 7-11a7 7 0 0 0-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.500"/>',
    ];
@endphp
<svg {{ $attributes->merge(['class' => 'size-6']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{!! $paths[$name] ?? $paths['question'] !!}</svg>

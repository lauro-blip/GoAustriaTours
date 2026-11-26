{{-- Heading element. Its level follows the page outline: under the section's own heading it starts at h3, without one at h2 (no skipped levels). --}}
@if(filled($e['text'] ?? null))
    @php
        $size = $e['size'] ?? 'm';
        $tag = ($secHasHead ?? true) ? (['l' => 'h3', 'm' => 'h3', 's' => 'h4'][$size] ?? 'h3') : (['l' => 'h2', 'm' => 'h2', 's' => 'h3'][$size] ?? 'h2');
    @endphp
    <{{ $tag }} class="el-h el-h-{{ $size }}"{{ $ed($ep.'text') }}>{{ $e['text'] }}</{{ $tag }}>
@endif

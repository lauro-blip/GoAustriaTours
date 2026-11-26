@php $lines = collect(preg_split('/\R/', (string) ($e['items'] ?? '')))->map(fn ($l) => trim($l))->filter()->values(); $icon = $e['icon'] ?? 'check'; @endphp
@if($lines->isNotEmpty())
    @if($icon === 'number')
        <ol class="el-list el-list-number">@foreach($lines as $l)<li>{{ $l }}</li>@endforeach</ol>
    @else
        <ul class="el-list el-list-{{ $icon }}">@foreach($lines as $l)<li>{{ $l }}</li>@endforeach</ul>
    @endif
@endif

{{-- Click-to-load video: only a cover and a play button are on the page; YouTube (privacy mode) / Vimeo loads when pressed (home.js [data-video]). --}}
@php $embed = \App\Support\Blocks::videoEmbed((string) ($e['url'] ?? '')); $poster = \App\Support\Blocks::image($e['poster'] ?? null); $vt = filled($e['title'] ?? null) ? $e['title'] : 'Video'; @endphp
@if($embed)
<div class="el-video" data-video="{{ $embed }}" data-title="{{ $vt }}">
    @if($poster)<img src="{{ $poster['src'] }}" alt="" loading="lazy">@endif
    <button type="button" class="el-play" aria-label="Play video: {{ $vt }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z" fill="currentColor"/></svg></button>
</div>
@endif

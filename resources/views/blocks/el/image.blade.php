@php $img = \App\Support\Blocks::image($e['image'] ?? null); $link = (string) ($e['link'] ?? ''); @endphp
@if($img)
<figure class="el-img r-{{ $e['ratio'] ?? 'land' }}">
    @if($link !== '')<a href="{{ $link }}">@endif<img src="{{ $img['src'] }}" @if($img['srcset']) srcset="{{ $img['srcset'] }}" sizes="(min-width: 1000px) 50vw, 100vw" @endif alt="{{ $img['alt'] }}" loading="lazy">@if($link !== '')</a>@endif
    @if(filled($e['caption'] ?? null))<figcaption{{ $ed($ep.'caption') }}>{{ $e['caption'] }}</figcaption>@endif
</figure>
@endif

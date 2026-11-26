@php $photos = collect($d['items'] ?? [])->map(fn ($g) => ['img' => \App\Support\Blocks::image($g['image'] ?? null), 'caption' => $g['caption'] ?? ''])->filter(fn ($g) => $g['img']); @endphp
@if($photos->isNotEmpty())
<section class="sec blk {{ $tone }}"{{ $sx }}>
    <div class="wrap">
        @include('blocks._head')
        <div class="blk-gal">
            @foreach($photos as $g)
                <figure>
                    <img src="{{ $g['img']['src'] }}" @if($g['img']['srcset']) srcset="{{ $g['img']['srcset'] }}" sizes="(min-width: 1000px) 33vw, 50vw" @endif alt="{{ $g['img']['alt'] }}" loading="lazy">
                    @if($g['caption'] !== '')<figcaption{{ $ed('items.'.$loop->index.'.caption') }}>{{ $g['caption'] }}</figcaption>@endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

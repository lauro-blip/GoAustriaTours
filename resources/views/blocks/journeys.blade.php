@php $list = \App\Support\Blocks::journeys((string) ($d['filter'] ?? 'all'), (int) ($d['limit'] ?? 6)); @endphp
@if($list->isNotEmpty())
<section class="sec blk {{ $tone }}"{{ $sx }}>
    <div class="wrap">
        @include('blocks._head')
        <div class="grid">
            @foreach($list as $p)<div class="cell">@include('pages.partials.package-card', ['f' => \App\Support\Places::card($p)])</div>@endforeach
        </div>
    </div>
</section>
@endif

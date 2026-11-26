{{-- Home: six more journey cards (only when there are more than six journeys). --}}
@if($popular->isNotEmpty())
<section class="sec tint">
    <div class="wrap">
        @include('home._head', ['oneline' => true])
        <div class="grid">@foreach($popular as $f)<div class="cell">@include('pages.partials.package-card', ['f' => $f])</div>@endforeach</div>
    </div>
</section>
@endif

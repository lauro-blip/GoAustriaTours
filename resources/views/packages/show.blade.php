@php
    $imgUrl = $package->hero_image ? asset('storage/'.$package->hero_image) : asset('images/packages/'.$package->slug.'.webp');
    $price = $package->price_from ? '$'.number_format($package->price_from) : null;
    $placesOn = \App\Support\Places::forPackage($package);
    $hasDesc = trim(strip_tags((string) $package->description)) !== '' && ! str_contains((string) $package->description, '[CONFIRM');
    $facts = array_values(array_filter([
        ['Duration', ($package->nights + 1).' days · '.$package->nights.' nights'],
        $package->tourType ? ['Style', $package->tourType->name] : null,
        $package->destination ? ['Region', $package->destination->name] : null,
        ['From', $price ? $price.' per person' : 'Price on request'],
    ]));
    $bTitle = $package->title;
    $bEyebrow = trim(($package->tourType?->name ?? '').' · '.$package->nights.' nights', ' ·');
    $bLead = $package->summary;
    $bImage = $imgUrl;
    $bCrumbs = [['Journeys', route('packages.index')], [$package->title, null]];
@endphp
@extends('layouts.site', ['noStickyBar' => true,
    'metaTitle' => $package->seo_title ?: $package->title.' | '.($site['brand_name'] ?? ''),
    'metaDescription' => $package->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $package->summary), 155),
    'canonical' => route('packages.show', $package),
    'noindex' => $package->noindex,
    'ogImage' => $package->social_image ? asset('storage/'.$package->social_image) : $imgUrl,
])

@push('jsonld')
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::trip($package), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
<script type="application/ld+json">{!! json_encode(\App\Support\Schema::breadcrumbs([['Home', route('home')], ['Packages', route('packages.index')], [$package->title, route('packages.show', $package)]]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@php $faqSchema = \App\Support\Schema::faq($package->faqs ?: []); @endphp
@if($faqSchema)
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
@endpush

@section('content')
@include('pages.partials.banner')

<section class="factsbar" aria-label="Tour at a glance">
    <div class="wrap"><dl>@foreach($facts as [$fl, $fv])<div><dt>{{ $fl }}</dt><dd>{{ $fv }}</dd></div>@endforeach</dl></div>
</section>

<section class="sec">
    <div class="wrap pk">
        <div class="pk-main">
            @if($hasDesc)<div class="prose">{!! $package->description !!}</div>@endif

            @if($package->highlights)
                <h2 class="pk-h">Highlights</h2>
                <ul class="pk-hl">@foreach($package->highlights as $h)<li>{{ $h }}</li>@endforeach</ul>
            @endif

            @if(count($placesOn))
                <h2 class="pk-h">Places on this journey</h2>
                @include('pages.partials.place-cards', ['places' => $placesOn])
            @endif

            @if($package->itinerary)
                <h2 class="pk-h">Day by day</h2>
                <ol class="tl">@foreach($package->itinerary as $day)<li><span class="tl-n" aria-hidden="true">{{ $loop->iteration }}</span><div><h3>{{ $day['title'] }}</h3><p>{{ $day['body'] }}</p></div></li>@endforeach</ol>
            @endif

            @if($package->inclusions || $package->exclusions)
                <div class="incl">
                    @if($package->inclusions)<div><h2>Included</h2><ul class="yes">@foreach($package->inclusions as $i)<li>{{ $i }}</li>@endforeach</ul></div>@endif
                    @if($package->exclusions)<div><h2>Not included</h2><ul class="no">@foreach($package->exclusions as $i)<li>{{ $i }}</li>@endforeach</ul></div>@endif
                </div>
            @endif

            @if($package->faqs)
                <h2 class="pk-h">Questions about this tour</h2>
                <div class="faq">@foreach($package->faqs as $f)<details><summary>{{ $f['question'] }}</summary><p>{{ $f['answer'] }}</p></details>@endforeach</div>
            @endif
        </div>

        <aside class="pk-side" aria-label="Enquire about this tour">
            <div class="pk-card">
                <p class="pk-from">{{ $price ? 'Starting from' : 'Price' }}</p>
                <p class="pk-price">{{ $price ?: 'On request' }}@if($price)<small> per person</small>@endif</p>
                <h2>Request your personal quote</h2>
                @include('pages.partials.quote-form', ['formName' => 'package-quote', 'package' => $package, 'uid' => 'pq', 'button' => 'Request my quote'])
            </div>
            <div class="pk-card">
                <h2>Have a question first?</h2>
                @include('pages.partials.quote-form', ['formName' => 'ask-question', 'package' => $package, 'uid' => 'aq', 'button' => 'Ask us a question', 'qShortDummy' => true, 'qfShort' => true])
            </div>
        </aside>
    </div>
</section>

<div class="pbar">
    <p><small>{{ $price ? 'Starting from' : 'Price' }}</small><b>{{ $price ?: 'On request' }}</b></p>
    <a class="btn sm" href="#qf-pq" data-quote data-package-id="{{ $package->id }}" data-package="{{ $package->title }}">Enquire</a>
</div>
@endsection

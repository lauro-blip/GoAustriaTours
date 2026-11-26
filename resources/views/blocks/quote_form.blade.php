<section class="sec blk {{ $tone }}" id="enquire-{{ $i }}">
    <div class="wrap blk-qf">
        <div class="blk-qf-copy">
            @if(filled($d['eyebrow'] ?? null))<p class="pk-k">{{ $d['eyebrow'] }}</p>@endif
            <h2 class="h">{{ filled($d['heading'] ?? null) ? $d['heading'] : 'Request your free quote' }}</h2>
            @if(filled($d['text'] ?? null))<p class="blk-intro">{{ $d['text'] }}</p>@endif
        </div>
        <div class="pk-card blk-qf-form">
            @include('pages.partials.quote-form', ['formName' => 'contact', 'uid' => 'blk-'.$i, 'button' => filled($d['button_label'] ?? null) ? $d['button_label'] : 'Send enquiry'])
        </div>
    </div>
</section>

@if(filled($e['label'] ?? null))
    @php $link = (string) ($e['link'] ?? ''); $cls = ['gold' => 'btn', 'outline' => 'btn el-btn-o', 'link' => 'link'][$e['style'] ?? 'gold'] ?? 'btn'; @endphp
    <p class="el-btn">
        @if($link !== '')
            <a class="{{ $cls }}" href="{{ $link }}"><span{{ $ed($ep.'label') }}>{{ $e['label'] }}</span>@if($cls !== 'link') <i aria-hidden="true">&rarr;</i>@endif</a>
        @else
            <a class="{{ $cls }}" href="{{ route('contact') }}" data-quote><span{{ $ed($ep.'label') }}>{{ $e['label'] }}</span>@if($cls !== 'link') <i aria-hidden="true">&rarr;</i>@endif</a>
        @endif
    </p>
@endif

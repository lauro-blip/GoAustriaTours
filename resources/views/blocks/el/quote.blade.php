@if(filled($e['text'] ?? null))
<figure class="el-quote">
    <blockquote{{ $ed($ep.'text') }}>{{ $e['text'] }}</blockquote>
    @if(filled($e['author'] ?? null))<figcaption{{ $ed($ep.'author') }}>{{ $e['author'] }}</figcaption>@endif
</figure>
@endif

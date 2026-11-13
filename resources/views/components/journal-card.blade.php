@props(['post'])
<article class="group flex h-full flex-col overflow-hidden rounded-[var(--radius-card)] border border-line bg-white shadow-card">
    <a href="{{ route('journal.show', $post) }}" tabindex="-1" aria-hidden="true" class="block aspect-[16/10] overflow-hidden bg-brand-soft">
        <img src="{{ $post->imageUrl() }}" alt="{{ $post->image_alt }}" width="800" height="600" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
    </a>
    <div class="flex flex-1 flex-col p-6">
        <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-accent-text">
            @if($post->category)<span>{{ $post->category }}</span><span aria-hidden="true">&middot;</span>@endif
            <time datetime="{{ $post->published_at?->toDateString() }}" class="font-medium normal-case tracking-normal text-muted">{{ $post->published_at?->format('M j, Y') }}</time>
        </p>
        <h3 class="mt-2 text-2xl"><a href="{{ route('journal.show', $post) }}" class="hover:underline decoration-accent decoration-2 underline-offset-8">{{ $post->title }}</a></h3>
        <p class="mt-2 text-sm text-muted">{{ $post->excerpt }}</p>
        <a href="{{ route('journal.show', $post) }}" class="mt-auto inline-flex items-center gap-1 pt-4 text-sm font-semibold text-brand underline-offset-4 hover:underline" aria-label="Read {{ $post->title }}">Read article <span aria-hidden="true">&rarr;</span></a>
    </div>
</article>

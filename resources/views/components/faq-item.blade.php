@props(['question'])
<details {{ $attributes->merge(['class' => 'group border-b border-line py-4']) }}>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-lg font-semibold text-brand">
        <span>{{ $question }}</span>
        <x-icon name="chevron" class="size-5 shrink-0 transition group-open:rotate-180" />
    </summary>
    <div class="mt-3 text-muted leading-relaxed">{{ $slot }}</div>
</details>

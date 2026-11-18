@props(['question', 'number' => null])
<details {{ $attributes->merge(['class' => 'group border-b border-line transition-colors duration-300 open:border-accent/60']) }}>
    <summary class="flex cursor-pointer list-none items-center gap-5 py-6 text-brand marker:hidden [&::-webkit-details-marker]:hidden sm:gap-7 sm:py-7">
        @if($number)<span class="hidden w-8 shrink-0 font-display text-xl italic text-accent-text sm:block" aria-hidden="true">{{ $number }}</span>@endif
        <span class="flex-1 font-display text-xl font-semibold leading-snug transition-colors group-hover:text-accent-text sm:text-2xl">{{ $question }}</span>
        <span class="relative flex size-10 shrink-0 items-center justify-center rounded-full border border-accent/70 text-brand transition duration-300 group-hover:bg-brand group-hover:text-white group-open:border-brand group-open:bg-brand group-open:text-white" aria-hidden="true">
            <span class="absolute h-px w-4 bg-current"></span>
            <span class="absolute h-4 w-px bg-current transition duration-300 group-open:rotate-90 group-open:opacity-0"></span>
        </span>
    </summary>
    <div class="pb-7 leading-relaxed text-brand/85 sm:pb-8 {{ $number ? 'sm:pl-[3.75rem]' : '' }} sm:pr-[4.5rem]"><div class="border-l border-accent pl-5">{{ $slot }}</div></div>
</details>

{{-- Hero quiz. Steps/options come from the database (quiz_steps / quiz_options) and are edited in /admin/quiz. --}}
@props(['steps'])
@php
    $icons = collect($steps)->pluck('options')->flatten(1)->pluck('icon')->filter()->unique()
        ->mapWithKeys(fn ($n) => [$n => \Illuminate\Support\Facades\Blade::render('<x-icon name="'.e($n).'" class="size-7" :stroke="1.3" />')])->all();
    $failed = old('form_name') === 'hero-quiz';
    $promise = trim((string) \App\Models\Setting::get('response_promise', ''));
    if (str_contains($promise, '[CONFIRM')) { $promise = ''; }
@endphp
<section id="quiz" aria-labelledby="quiz-title" class="relative rounded-sm border border-accent/60 bg-ivory p-7 text-ink shadow-[0_40px_90px_-20px_rgb(0_0_0/0.65)] sm:p-10">
    <span class="pointer-events-none absolute inset-2 border border-accent/25" aria-hidden="true"></span>
    <span class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-accent to-transparent" aria-hidden="true"></span>
    <p class="text-center text-xs font-semibold uppercase tracking-[0.3em] text-accent-text">Bespoke itineraries</p>
    <h2 id="quiz-title" class="mt-2 text-center text-[32px] normal-case sm:text-[44px]">Plan Your Journey</h2>
    <p class="mx-auto mt-3 max-w-xs text-center text-sm leading-relaxed text-brand/80">A few questions, then a personal itinerary.</p>

    {{-- JavaScript enabled: step-by-step quiz --}}
    <div x-data="quiz({ steps: @js($steps), icons: @js($icons), formName: 'hero-quiz', initial: @js($failed ? (array) old('quiz_answers') : (object) []), startAtContact: @js($failed) })" x-cloak>
        <p class="mt-6 flex items-center justify-center gap-3 font-display text-2xl italic text-accent-text" aria-live="polite"><span class="h-px w-8 bg-accent/60" aria-hidden="true"></span><span><span class="sr-only">Step </span><span x-text="String(step + 1).padStart(2, '0')"></span><span class="text-brand/60"> / <span class="sr-only">of </span><span x-text="String(total).padStart(2, '0')"></span></span></span><span class="h-px w-8 bg-accent/60" aria-hidden="true"></span></p>

        <template x-if="!isContact">
            <div class="mt-5">
                <h3 class="font-display text-[1.9rem] !font-semibold normal-case leading-tight text-center text-brand" x-text="current.question" tabindex="-1"></h3>

                <template x-if="current.type === 'tiles'">
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <template x-for="o in current.options" :key="o.value">
                            <button type="button" @click="choose(o.value)"
                                    class="group flex min-h-[8rem] flex-col items-center justify-center gap-3 rounded-sm border border-line bg-white px-3 py-5 text-center text-base font-medium leading-snug text-brand transition duration-200 hover:-translate-y-0.5 hover:border-accent hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cta motion-reduce:transition-none motion-reduce:hover:translate-y-0"
                                    :class="(picked === o.value || answers[current.key] === o.value) && '!border-brand !bg-brand !text-white shadow-md'">
                                <span x-show="o.icon" class="flex size-12 items-center justify-center rounded-full border border-accent/50 text-brand transition duration-200 group-hover:border-accent group-hover:bg-accent/10" :class="(picked === o.value || answers[current.key] === o.value) && '!border-accent !text-accent'" x-html="icons[o.icon]" aria-hidden="true"></span>
                                <span x-text="o.label"></span>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="current.type === 'dropdown'">
                    <div class="mt-6">
                        <label class="sr-only" for="quiz-select" x-text="current.question"></label>
                        <select id="quiz-select" x-model="answers[current.key]" @change="answers[current.key] && next()" class="w-full rounded-sm border border-line bg-white px-4 py-4 text-base focus:border-accent">
                            <option value="">Select…</option>
                            <template x-for="o in current.options" :key="o.value"><option :value="o.value" x-text="o.label"></option></template>
                        </select>
                    </div>
                </template>
            </div>
        </template>

        <div x-show="isContact" class="mt-4">
            <h3 class="font-display text-3xl !font-medium normal-case leading-tight text-center text-brand">Where should we send your <em class="text-accent-text">personal quote?</em></h3>
            <div class="mt-4">
                <x-enquiry-form form-name="hero-quiz" uid="quiz" button="Request My Quote">
                    <template x-for="(v, k) in answers" :key="k"><input type="hidden" :name="`quiz_answers[${k}]`" :value="v"></template>
                </x-enquiry-form>
            </div>
        </div>

        <button type="button" x-show="step > 0" x-cloak @click="back()" class="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-brand underline decoration-accent decoration-2 underline-offset-8 hover:text-accent-text">&larr; Back</button>
    </div>

    {{-- No JavaScript: plain contact form so no lead is lost. --}}
    <noscript>
        <p class="mt-3 text-center text-sm text-muted">Tell us about your trip and we will send a free quote.</p>
        <div class="mt-4"><x-enquiry-form form-name="hero-quiz" uid="quiz-nojs" :nojs="true" /></div>
    </noscript>

    <p class="mt-5 flex items-center justify-center gap-1.5 text-xs text-muted"><x-icon name="lock" class="size-4" /> Takes less than 30 seconds &middot; No obligation</p>
    @if($promise !== '')<p class="mt-2 text-center text-sm font-medium text-brand">{{ $promise }}</p>@endif
</section>

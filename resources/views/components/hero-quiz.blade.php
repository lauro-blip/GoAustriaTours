{{-- Hero quiz. Steps/options come from the database (quiz_steps / quiz_options) and are edited in /admin/quiz. --}}
@props(['steps'])
@php
    $icons = collect($steps)->pluck('options')->flatten(1)->pluck('icon')->filter()->unique()
        ->mapWithKeys(fn ($n) => [$n => \Illuminate\Support\Facades\Blade::render('<x-icon name="'.e($n).'" class="size-7" :stroke="1.3" />')])->all();
    $failed = old('form_name') === 'hero-quiz';
@endphp
<section id="quiz" aria-labelledby="quiz-title" class="rounded-2xl border-4 border-quiz bg-white p-5 sm:p-7 shadow-2xl text-ink">
    <h2 id="quiz-title" class="font-sans text-center text-lg font-extrabold uppercase tracking-wider text-brand">Plan Your Journey</h2>

    {{-- JavaScript enabled: step-by-step quiz --}}
    <div x-data="quiz({ steps: @js($steps), icons: @js($icons), formName: 'hero-quiz', initial: @js($failed ? (array) old('quiz_answers') : (object) []), startAtContact: @js($failed) })" x-cloak>
        <p class="mt-2 text-center text-[11px] font-medium uppercase tracking-[0.2em] text-muted" aria-live="polite">Step <span x-text="step + 1"></span> of <span x-text="total"></span></p>

        <template x-if="!isContact">
            <div class="mt-4">
                <h3 class="font-sans text-xl font-bold text-center text-brand" x-text="current.question" tabindex="-1"></h3>

                <template x-if="current.type === 'tiles'">
                    <div class="mt-6 grid grid-cols-2 gap-3.5">
                        <template x-for="o in current.options" :key="o.value">
                            <button type="button" @click="choose(o.value)"
                                    class="group flex min-h-[7.5rem] flex-col items-center justify-center gap-3 rounded-xl border border-line bg-white px-3 py-5 text-center text-sm font-semibold leading-snug text-brand transition duration-200 hover:-translate-y-0.5 hover:border-cta hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cta motion-reduce:transition-none motion-reduce:hover:translate-y-0"
                                    :class="(picked === o.value || answers[current.key] === o.value) && 'border-cta bg-brand-soft shadow-md'">
                                <span x-show="o.icon" class="flex size-14 items-center justify-center rounded-full bg-brand-soft text-brand transition duration-200 group-hover:bg-cta/15" :class="picked === o.value && 'bg-cta text-white'" x-html="icons[o.icon]" aria-hidden="true"></span>
                                <span x-text="o.label"></span>
                            </button>
                        </template>
                    </div>
                </template>

                <template x-if="current.type === 'dropdown'">
                    <div class="mt-6">
                        <label class="sr-only" for="quiz-select" x-text="current.question"></label>
                        <select id="quiz-select" x-model="answers[current.key]" @change="answers[current.key] && next()" class="w-full rounded-lg border border-line bg-white px-4 py-3.5 text-base">
                            <option value="">Select…</option>
                            <template x-for="o in current.options" :key="o.value"><option :value="o.value" x-text="o.label"></option></template>
                        </select>
                    </div>
                </template>
            </div>
        </template>

        <div x-show="isContact" class="mt-4">
            <h3 class="font-sans text-xl font-bold text-center text-brand">Where should we send your free quote?</h3>
            <ul class="mt-3 flex flex-wrap gap-1.5 text-xs" aria-label="Your answers">
                <template x-for="s in steps" :key="s.key"><li x-show="answers[s.key]" class="rounded-full bg-brand-soft px-2.5 py-1 text-brand" x-text="labelFor(s)"></li></template>
            </ul>
            <div class="mt-4">
                <x-enquiry-form form-name="hero-quiz" uid="quiz" button="Request My Quote">
                    <template x-for="(v, k) in answers" :key="k"><input type="hidden" :name="`quiz_answers[${k}]`" :value="v"></template>
                </x-enquiry-form>
            </div>
        </div>

        <button type="button" x-show="step > 0" x-cloak @click="back()" class="mt-6 text-sm text-brand underline underline-offset-4">&larr; Back</button>
    </div>

    {{-- No JavaScript: plain contact form so no lead is lost. --}}
    <noscript>
        <p class="mt-3 text-center text-sm text-muted">Tell us about your trip and we will send a free quote.</p>
        <div class="mt-4"><x-enquiry-form form-name="hero-quiz" uid="quiz-nojs" :nojs="true" /></div>
    </noscript>

    <p class="mt-6 flex items-center justify-center gap-1.5 text-xs text-muted"><x-icon name="lock" class="size-4" /> Takes less than 30 seconds</p>
</section>

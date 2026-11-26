@extends('admin.layout')
@section('title', 'Design')
@section('heading', 'Design')
@section('actions')<x-button :href="route('home')" variant="outline" target="_blank" rel="noopener">View site &nearr;</x-button>@endsection

@php
    $fontsH = collect(\App\Support\Design::FONTS_HEADING)->map(fn ($f) => $f[1]);
    $fontsB = collect(\App\Support\Design::FONTS_BODY)->map(fn ($f) => $f[1]);
    $card = 'rounded-xl border border-line bg-white p-5 sm:p-6';
    $in = 'w-full rounded-lg border border-line bg-white px-3 py-2';
@endphp

@section('content')
<style>
    @font-face { font-family: "Inter Variable"; font-style: normal; font-display: swap; font-weight: 100 900; src: url("/fonts/inter-latin-wght-normal.woff2") format("woff2"); }
    @font-face { font-family: "Playfair Display Variable"; font-style: normal; font-display: swap; font-weight: 400 900; src: url("/fonts/playfair-display-latin-wght-normal.woff2") format("woff2"); }
    @font-face { font-family: "Playfair Display Variable"; font-style: italic; font-display: swap; font-weight: 400 900; src: url("/fonts/playfair-display-latin-wght-italic.woff2") format("woff2"); }
</style>

<form method="POST" action="{{ route('admin.design.update') }}" id="design-form"
      x-data="{
        d: @js($design), presets: @js($presets), defaults: @js($defaults), fh: @js($fontsH), fb: @js($fontsB), dirty: false,
        apply(p) { Object.keys(this.presets[p]).filter(k => k !== 'label').forEach(k => this.d[k] = this.presets[p][k]); this.dirty = true; },
        lum(h) { const c = [1, 3, 5].map(i => parseInt(h.slice(i, i + 2), 16) / 255).map(v => v <= 0.03928 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4); return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2]; },
        ratio(a, b) { if (! /^#[0-9a-f]{6}$/i.test(a) || ! /^#[0-9a-f]{6}$/i.test(b)) return 0; const [x, y] = [this.lum(a), this.lum(b)]; return Math.round((Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05) * 100) / 100; },
        get checks() { return [
            ['Body text on the background', this.ratio(this.d.text, this.d.bg), 4.5],
            ['Headings on the background', this.ratio(this.d.navy, this.d.bg), 4.5],
            ['Button text on buttons', this.ratio(this.d.navy, this.d.accent), 4.5],
            ['Small accent text on the background', this.ratio(this.d.accent_text, this.d.bg), 4.5],
            ['White text on dark sections', this.ratio('#ffffff', this.d.dark), 4.5],
        ]; },
        get radius() { return { square: '0', soft: '6px', pill: '999px' }[this.d.buttons]; },
      }"
      @input="dirty = true" @change="dirty = true" @submit="dirty = false"
      class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(0,460px)]">
    @csrf @method('PUT')

    <div class="min-w-0 space-y-6">
        {{-- ============ PRESETS ============ --}}
        <section class="{{ $card }}">
            <h2 class="text-2xl">Colour palettes</h2>
            <p class="mb-4 text-sm text-muted">Start from a ready-made palette (each one keeps text and buttons readable), then fine-tune below.</p>
            <div class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-3">
                <template x-for="[key, p] in Object.entries(presets)" :key="key">
                    <button type="button" class="flex items-center gap-3 rounded-lg border border-line p-3 text-left hover:border-brand" @click="apply(key)">
                        <span class="flex shrink-0 overflow-hidden rounded-md border border-line">
                            <template x-for="c in [p.navy, p.dark, p.accent, p.bg, p.tint]"><span class="block h-9 w-5" :style="'background:' + c"></span></template>
                        </span>
                        <span class="text-sm font-semibold text-brand" x-text="p.label"></span>
                    </button>
                </template>
            </div>
        </section>

        {{-- ============ COLOURS ============ --}}
        <section class="{{ $card }}">
            <h2 class="mb-4 text-2xl">Colours</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach(\App\Support\Design::COLORS as $key => [$label, $help])
                    <div>
                        <label class="mb-1 block text-sm font-medium" for="c-{{ $key }}">{{ $label }}</label>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="d.{{ $key }}" class="h-11 w-14 shrink-0 cursor-pointer rounded border border-line bg-white p-1" aria-label="{{ $label }} picker">
                            <input id="c-{{ $key }}" name="{{ $key }}" x-model="d.{{ $key }}" pattern="#[0-9a-fA-F]{6}" maxlength="7" class="{{ $in }} font-mono uppercase" required>
                            <button type="button" class="shrink-0 text-xs text-muted underline" x-show="d.{{ $key }} !== defaults.{{ $key }}" @click="d.{{ $key }} = defaults.{{ $key }}; dirty = true" title="Back to the brand colour">reset</button>
                        </div>
                        @if($help)<p class="mt-1 text-xs text-muted">{{ $help }}</p>@endif
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ============ TYPE & BUTTONS ============ --}}
        <section class="{{ $card }}">
            <h2 class="mb-4 text-2xl">Fonts, text and buttons</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <div><label class="mb-1 block text-sm font-medium" for="fh">Heading font</label>
                    <select id="fh" name="font_heading" x-model="d.font_heading" class="{{ $in }}">@foreach(\App\Support\Design::FONTS_HEADING as $k => [$l])<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div><label class="mb-1 block text-sm font-medium" for="fb">Body font</label>
                    <select id="fb" name="font_body" x-model="d.font_body" class="{{ $in }}">@foreach(\App\Support\Design::FONTS_BODY as $k => [$l])<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div><label class="mb-1 block text-sm font-medium" for="hc">Section headings</label>
                    <select id="hc" name="heading_case" x-model="d.heading_case" class="{{ $in }}">@foreach(\App\Support\Design::HEADING_CASE as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <div><label class="mb-1 block text-sm font-medium" for="bw">Body text weight</label>
                    <select id="bw" name="body_weight" x-model="d.body_weight" class="{{ $in }}">@foreach(\App\Support\Design::BODY_WEIGHT as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                <fieldset class="sm:col-span-2"><legend class="mb-2 text-sm font-medium">Button shape</legend>
                    <div class="flex flex-wrap gap-3">
                        @foreach(\App\Support\Design::BUTTONS as $k => $l)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 has-[:checked]:border-brand has-[:checked]:bg-brand-soft">
                                <input type="radio" name="buttons" value="{{ $k }}" x-model="d.buttons" class="accent-brand">
                                <span class="inline-block px-4 py-1.5 text-xs font-bold uppercase tracking-wider" :style="'background:' + d.accent + ';color:' + d.navy + ';border-radius:' + {square: '0', soft: '6px', pill: '999px'}['{{ $k }}']">Button</span>
                                <span class="text-sm">{{ $l }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            </div>
            <p class="mt-4 text-xs text-muted">Only fonts already on the site (or built into visitors' devices) are offered, so pages stay fast for Google Ads.</p>
        </section>
    </div>

    {{-- ============ LIVE PREVIEW + ACTIONS ============ --}}
    <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
        <section class="overflow-hidden rounded-xl border border-line bg-white">
            <p class="border-b border-line px-5 py-3 text-sm font-semibold">Live preview</p>
            <div :style="`background:${d.bg};color:${d.text};font-family:${fb[d.font_body]};font-weight:${d.body_weight}`">
                <div class="flex items-center justify-between px-5 py-3" :style="`background:${d.dark};color:#fff`">
                    <span :style="`font-family:${fh[d.font_heading]};font-size:20px;font-style:italic`">GoAustria</span>
                    <span class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest" :style="`background:${d.accent};color:${d.navy};border-radius:${radius}`">Plan your journey</span>
                </div>
                <div class="space-y-3 px-5 py-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[.2em]" :style="`color:${d.accent_text}`">Handpicked journeys</p>
                    <h3 class="text-[30px] leading-tight" :style="`font-family:${fh[d.font_heading]};color:${d.navy};text-transform:${d.heading_case === 'upper' ? 'uppercase' : 'none'};font-weight:400`">Our top-rated <em :style="`color:${d.accent_text}`">Austrian tours</em></h3>
                    <p class="text-[15px] leading-relaxed">Hand-built journeys through the cities, lakes and Alps, planned around your dates by a personal travel specialist.</p>
                    <div class="flex flex-wrap items-center gap-4 pt-1">
                        <span class="inline-flex px-5 py-3 text-xs font-bold uppercase tracking-wider" :style="`background:${d.accent};color:${d.navy};border-radius:${radius}`">Get a free quote &rarr;</span>
                        <span class="border-b-2 pb-0.5 text-xs font-bold uppercase tracking-wider" :style="`color:${d.navy};border-color:${d.accent}`">View all</span>
                    </div>
                </div>
                <div class="px-5 pb-6" :style="`background:${d.tint}`">
                    <div class="translate-y-3 border bg-white p-4" :style="`border-color:${d.line};border-top:3px solid ${d.accent}`">
                        <p class="text-[11px] uppercase tracking-widest" :style="`color:${d.accent_text}`">Tyrol &amp; the Alps</p>
                        <p class="mt-1 text-lg" :style="`font-family:${fh[d.font_heading]};color:${d.navy}`">Alpine Lakes Self Drive</p>
                        <p class="mt-1 text-sm">11 days · 10 nights · From <b :style="`color:${d.navy}`">$2,890</b></p>
                    </div>
                </div>
                <div class="px-5 py-5 text-sm" :style="`background:${d.dark};color:rgba(255,255,255,.85)`">Footer and dark sections use this colour.</div>
            </div>
        </section>

        <section class="{{ $card }} space-y-3">
            <h2 class="text-xl">Readability</h2>
            <ul class="space-y-1.5 text-sm">
                <template x-for="[label, r, min] in checks" :key="label">
                    <li class="flex items-center justify-between gap-3">
                        <span x-text="label"></span>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold" :class="r >= min ? 'bg-green-100 text-green-900' : 'bg-red-100 text-red-900'" x-text="(r >= min ? 'OK ' : 'Too low ') + r + ':1'"></span>
                    </li>
                </template>
            </ul>
            <p class="text-xs text-muted">Text needs at least 4.5:1 to be comfortable to read (WCAG AA). Low contrast also hurts Lighthouse scores.</p>
        </section>

        <section class="{{ $card }} space-y-3">
            <p x-show="dirty" class="text-sm text-amber-800">You have unsaved changes.</p>
            <x-button type="submit" variant="secondary" class="w-full">Save design</x-button>
            <button type="submit" formaction="{{ route('admin.design.preview') }}" formmethod="POST" formtarget="_blank" class="w-full rounded-full border-2 border-brand px-6 py-2.5 text-sm font-semibold text-brand hover:bg-brand-soft">Preview on the site &nearr;</button>
            <p class="text-xs text-muted">The preview opens the home page in a new tab with these settings, visible only to you, until you save.</p>
            <button type="submit" form="design-reset" class="w-full rounded border border-red-300 px-3 py-2 text-sm text-red-800">Reset to the GoAustriaTours brand</button>
        </section>
    </aside>
</form>
<form id="design-reset" method="POST" action="{{ route('admin.design.reset') }}" onsubmit="return confirm('Reset all colours, fonts and buttons to the brand design?')">@csrf</form>
@endsection

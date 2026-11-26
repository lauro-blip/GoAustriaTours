@extends('admin.layout')
@section('title', 'Menus')
@section('heading', 'Menus & footer')
@section('actions')<x-button :href="route('home')" variant="outline" target="_blank" rel="noopener">View site &nearr;</x-button>@endsection

@php
    $card = 'rounded-xl border border-line bg-white p-5 sm:p-6';
    $in = 'w-full rounded-lg border border-line bg-white px-3 py-2';
    $sm = 'rounded border border-line bg-white px-2 py-1 text-sm disabled:opacity-30';
@endphp

@section('content')
<form method="POST" action="{{ route('admin.menus.update') }}" @submit="dirty = false"
      x-data="menuEditor(@js(['header' => $header, 'footer' => $footer, 'choices' => $choices, 'headerLabels' => $headerLabels, 'footerLabels' => $footerLabels, 'mega' => $mega]))"
      class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
    @csrf @method('PUT')
    <input type="hidden" name="header_json" :value="json(h)">
    <input type="hidden" name="footer_json" :value="json(f)">


    <div class="min-w-0 space-y-6">
        {{-- ============ HEADER ============ --}}
        <section class="{{ $card }}">
            <h2 class="text-2xl">Header menu</h2>
            <p class="mb-4 text-sm text-muted">Rename, reorder or hide the menu items. Items with a dropdown keep it (its content comes from your destinations, tour types, travel styles and pages). Add your own links anywhere. The mobile menu follows the same order.</p>
            <ol class="space-y-2">
                <template x-for="(it, i) in h.items" :key="i">
                    <li class="flex flex-wrap items-center gap-2 rounded-lg border p-3" :class="it.hidden ? 'border-dashed border-secondary bg-sand/30 opacity-70' : 'border-line bg-white'">
                        <span class="grid size-7 shrink-0 place-items-center rounded-full bg-brand text-xs font-semibold text-white" x-text="i + 1"></span>
                        <div class="min-w-48 flex-1">
                            <input type="text" x-model="it.label" maxlength="40" class="{{ $in }}" :aria-label="'Menu label ' + (i + 1)" placeholder="Label">
                            <p class="mt-1 text-xs text-muted" x-show="it.key !== 'link'"><span x-text="'Built-in: ' + builtinName(it)"></span><span x-show="hasMega(it)"> (with dropdown)</span><span x-show="it.hidden" class="text-amber-800"> · hidden</span></p>
                            <div x-show="it.key === 'link'" class="mt-2 flex flex-wrap gap-2">
                                <select class="{{ $in }} max-w-56 text-sm" @change="pick(it, $event.target.value); $event.target.value = ''"><option value="">Pick a page…</option><template x-for="c in choices" :key="c.url"><option :value="c.url" x-text="c.label"></option></template></select>
                                <input type="text" x-model="it.url" maxlength="300" placeholder="/page or https://…" class="{{ $in }} min-w-48 flex-1 text-sm" aria-label="Link address">
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1">
                            <button type="button" class="{{ $sm }}" @click="move(h.items, i, -1)" :disabled="i === 0" aria-label="Move up">&uarr;</button>
                            <button type="button" class="{{ $sm }}" @click="move(h.items, i, 1)" :disabled="i === h.items.length - 1" aria-label="Move down">&darr;</button>
                            <button type="button" class="{{ $sm }}" @click="it.hidden = ! it.hidden" x-text="it.hidden ? 'Show' : 'Hide'"></button>
                            <button type="button" class="{{ $sm }} border-red-300 text-red-800" x-show="it.key === 'link'" @click="remove(h.items, i)">Remove</button>
                        </div>
                    </li>
                </template>
            </ol>
            <button type="button" class="mt-3 rounded-full border-2 border-brand px-4 py-1.5 text-sm font-semibold text-brand disabled:opacity-40" @click="addHeaderLink()" :disabled="h.items.length >= 12">+ Add a link</button>
            <p class="mt-2 text-xs text-muted">Tip: 6 or 7 items fit on one line on laptop screens.</p>

            <div class="mt-6 grid gap-4 border-t border-line pt-5 sm:grid-cols-2">
                <div><label class="mb-1 block text-sm font-medium" for="cta-l">Header button label</label><input id="cta-l" type="text" x-model="h.cta_label" maxlength="40" class="{{ $in }}" placeholder="Empty hides the button"></div>
                <div><label class="mb-1 block text-sm font-medium" for="cta-u">Header button link</label>
                    <input id="cta-u" type="text" x-model="h.cta_url" maxlength="300" class="{{ $in }}" placeholder="Empty opens the quote form">
                    <p class="mt-1 text-xs text-muted">Leave empty to open the quote form (best for Google Ads leads).</p></div>
            </div>
        </section>

        {{-- ============ FOOTER ============ --}}
        <section class="{{ $card }} space-y-5">
            <h2 class="text-2xl">Footer</h2>

            <div class="space-y-3 rounded-lg bg-sand/30 p-4">
                <h3 class="text-lg">Call to action above the footer</h3>
                <div><label class="mb-1 block text-sm font-medium" for="f-ct">Heading</label><input id="f-ct" type="text" x-model="f.cta_title" maxlength="120" class="{{ $in }}"></div>
                <div><label class="mb-1 block text-sm font-medium" for="f-cx">Text</label><input id="f-cx" type="text" x-model="f.cta_text" maxlength="240" class="{{ $in }}"></div>
                <div class="sm:max-w-xs"><label class="mb-1 block text-sm font-medium" for="f-cb">Button label (opens the quote form)</label><input id="f-cb" type="text" x-model="f.cta_button" maxlength="40" class="{{ $in }}"></div>
                <p class="text-xs text-muted">Some pages show their own heading here (a package, a tour type, a region); this is the default everywhere else.</p>
            </div>

            <div>
                <h3 class="mb-2 text-lg">Columns</h3>
                <ol class="space-y-2">
                    <template x-for="(col, i) in f.columns" :key="i">
                        <li class="rounded-lg border p-3" :class="col.hidden ? 'border-dashed border-secondary bg-sand/30 opacity-70' : 'border-line bg-white'">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="grid size-7 shrink-0 place-items-center rounded-full bg-brand-dark text-xs font-semibold text-white" x-text="i + 1"></span>
                                <div class="min-w-48 flex-1">
                                    <input type="text" x-model="col.title" maxlength="40" class="{{ $in }}" :placeholder="col.key === 'contact' || col.key === 'brand' ? 'Heading (optional)' : 'Heading'">
                                    <p class="mt-1 text-xs text-muted" x-show="col.key !== 'links'" x-text="'Built-in: ' + footerLabels[col.key] + (col.key === 'destinations' || col.key === 'styles' ? ' (links listed automatically)' : '')"></p>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    <button type="button" class="{{ $sm }}" @click="move(f.columns, i, -1)" :disabled="i === 0" aria-label="Move left">&uarr;</button>
                                    <button type="button" class="{{ $sm }}" @click="move(f.columns, i, 1)" :disabled="i === f.columns.length - 1" aria-label="Move right">&darr;</button>
                                    <button type="button" class="{{ $sm }}" @click="col.hidden = ! col.hidden" x-text="col.hidden ? 'Show' : 'Hide'"></button>
                                    <button type="button" class="{{ $sm }} border-red-300 text-red-800" x-show="col.key === 'links'" @click="remove(f.columns, i)">Remove</button>
                                </div>
                            </div>
                            <div x-show="col.key === 'brand'" class="mt-3"><label class="mb-1 block text-sm font-medium">Text under the logo</label><textarea x-model="f.brand_text" rows="2" maxlength="400" class="{{ $in }}"></textarea><p class="mt-1 text-xs text-muted">{brand} is replaced with your brand name.</p></div>
                            <div x-show="col.key === 'links'" class="mt-3 space-y-2">
                                <template x-for="(l, j) in col.links" :key="j">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <input type="text" x-model="l.label" maxlength="60" placeholder="Label" class="{{ $in }} max-w-48 text-sm">
                                        <select class="{{ $in }} max-w-44 text-sm" @change="pick(l, $event.target.value); $event.target.value = ''"><option value="">Pick a page…</option><template x-for="c in choices" :key="c.url"><option :value="c.url" x-text="c.label"></option></template></select>
                                        <input type="text" x-model="l.url" maxlength="300" placeholder="/page or https://…" class="{{ $in }} min-w-40 flex-1 text-sm">
                                        <button type="button" class="{{ $sm }}" @click="move(col.links, j, -1)" aria-label="Move up">&uarr;</button>
                                        <button type="button" class="{{ $sm }} border-red-300 text-red-800" @click="remove(col.links, j)" aria-label="Remove link">&times;</button>
                                    </div>
                                </template>
                                <button type="button" class="text-sm font-semibold text-brand underline" @click="addLink(col.links)" x-show="col.links.length < 10">+ Add link</button>
                            </div>
                        </li>
                    </template>
                </ol>
                <button type="button" class="mt-3 rounded-full border-2 border-brand px-4 py-1.5 text-sm font-semibold text-brand disabled:opacity-40" @click="addColumn()" :disabled="f.columns.length >= 8">+ Add a link column</button>
            </div>

            <div>
                <h3 class="mb-2 text-lg">Bottom bar links</h3>
                <div class="space-y-2">
                    <template x-for="(l, j) in f.bottom_links" :key="j">
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="text" x-model="l.label" maxlength="60" placeholder="Label" class="{{ $in }} max-w-48 text-sm">
                            <select class="{{ $in }} max-w-44 text-sm" @change="pick(l, $event.target.value); $event.target.value = ''"><option value="">Pick a page…</option><template x-for="c in choices" :key="c.url"><option :value="c.url" x-text="c.label"></option></template></select>
                            <input type="text" x-model="l.url" maxlength="300" placeholder="/page or https://…" class="{{ $in }} min-w-40 flex-1 text-sm">
                            <button type="button" class="{{ $sm }}" @click="move(f.bottom_links, j, -1)" :disabled="j === 0" aria-label="Move up">&uarr;</button>
                            <button type="button" class="{{ $sm }} border-red-300 text-red-800" @click="remove(f.bottom_links, j)" aria-label="Remove link">&times;</button>
                        </div>
                    </template>
                </div>
                <button type="button" class="mt-2 text-sm font-semibold text-brand underline" @click="addLink(f.bottom_links)" x-show="f.bottom_links.length < 12">+ Add link</button>
                <p class="mt-1 text-xs text-muted">Pages you place in "Footer links" (Admin &gt; Pages) are added after these automatically. Keep Terms and Privacy here: the consent checkbox links to them.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2"><label class="mb-1 block text-sm font-medium" for="f-cp">Copyright line</label><input id="f-cp" type="text" x-model="f.copyright" maxlength="160" class="{{ $in }}"><p class="mt-1 text-xs text-muted">{year} and {brand} are filled in automatically.</p></div>
                <div><label class="mb-1 block text-sm font-medium" for="f-cs">Sticky bar text (before the phone number)</label><input id="f-cs" type="text" x-model="f.consult_text" maxlength="80" class="{{ $in }}"></div>
                <div><label class="mb-1 block text-sm font-medium" for="f-cq">Sticky bar button</label><input id="f-cq" type="text" x-model="f.consult_button" maxlength="40" class="{{ $in }}"></div>
            </div>
        </section>
    </div>

    <aside class="space-y-4 xl:sticky xl:top-6 xl:self-start">
        <section class="{{ $card }} space-y-3">
            <p x-show="dirty" class="text-sm text-amber-800">You have unsaved changes.</p>
            <x-button type="submit" variant="secondary" class="w-full">Save menus</x-button>
            <button type="submit" form="menus-reset" class="w-full rounded border border-red-300 px-3 py-2 text-sm text-red-800">Reset to the original menus</button>
        </section>
        <section class="{{ $card }} text-sm text-muted space-y-2">
            <p><b class="text-ink">Phone, email and address</b> in the footer come from Admin &gt; Trust &amp; tracking. Social links come from Admin &gt; Settings.</p>
            <p><b class="text-ink">Links</b> can be a page on this site (start with /), a full https:// address, mailto: or tel:.</p>
        </section>
    </aside>
</form>
<form id="menus-reset" method="POST" action="{{ route('admin.menus.reset') }}" onsubmit="return confirm('Reset the header menu and footer to the original?')">@csrf</form>
@endsection

@extends('admin.layout')
@php
    $isNew = ! $page->exists;
    $custom = ! $page->is_system;
    $in = 'w-full rounded-lg border border-line bg-white px-3 py-2.5';
    $card = 'rounded-xl border border-line bg-white p-5 sm:p-6';
    $viewUrl = $page->exists && ($custom ? true : $page->slug !== 'thank-you') ? $page->url() : null;
@endphp
@section('title', $isNew ? 'New page' : 'Edit page')
@section('heading', $isNew ? 'New page' : 'Edit: '.$page->title)
@section('actions')
    <div class="flex flex-wrap gap-2">
        @if($viewUrl)<x-button :href="$viewUrl" variant="outline" target="_blank" rel="noopener">{{ $custom && ! $page->is_published ? 'Preview draft' : 'View page' }} &nearr;</x-button>@endif
        <x-button :href="route('admin.pages.index')" variant="outline">All pages</x-button>
    </div>
@endsection

@section('content')
<form method="POST" action="{{ $isNew ? route('admin.pages.store') : route('admin.pages.update', $page) }}" x-data="pageBuilder(@js($builder))" @submit="dirty = false" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
    @csrf
    @unless($isNew) @method('PUT') @endunless
    <input type="hidden" name="blocks_json" :value="json()">
    <input type="hidden" name="header_image" :value="headerImg.ref">

    <div class="min-w-0 space-y-6">
        {{-- ============ PAGE DETAILS ============ --}}
        <section class="{{ $card }} space-y-4">
            <h2 class="text-2xl">Page</h2>
            <div><label class="mb-1 block text-sm font-medium" for="title">{{ $page->slug === 'home' ? 'Page name (Admin only)' : 'Page title (shown as the main heading)' }}</label><input id="title" name="title" value="{{ old('title', $page->title) }}" required maxlength="190" class="{{ $in }}" @input="dirty = true"></div>
            @if($custom)
                <div><label class="mb-1 block text-sm font-medium" for="slug">Web address</label>
                    <div class="flex items-center overflow-hidden rounded-lg border border-line bg-white"><span class="bg-sand/60 px-3 py-2.5 text-sm text-muted">{{ preg_replace('#^https?://#', '', url('/')) }}/</span><input id="slug" name="slug" value="{{ old('slug', $page->slug) }}" maxlength="80" pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="made-from-the-title" class="min-w-0 flex-1 px-3 py-2.5 outline-none" @input="dirty = true"></div>
                    <p class="mt-1 text-xs text-muted">Leave empty to make it from the title. Lowercase letters, numbers and dashes. Changing it later breaks links to the old address.</p></div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-1 block text-sm font-medium" for="eyebrow">Small label above the title</label><input id="eyebrow" name="eyebrow" value="{{ old('eyebrow', $page->eyebrow) }}" maxlength="120" placeholder="e.g. Winter in Austria" class="{{ $in }}" @input="dirty = true"></div>
                    <div><label class="mb-1 block text-sm font-medium">Header photo</label>
                        <div class="flex items-center gap-3">
                            <button type="button" class="grid h-16 w-24 shrink-0 place-items-center overflow-hidden rounded-lg border border-dashed border-secondary bg-sand/50 text-xs text-muted" @click="openPicker(headerImg)"><img x-show="headerImg.ref" :src="thumb(headerImg.ref)" alt="" class="size-full object-cover"><span x-show="! headerImg.ref">Default</span></button>
                            <div class="flex flex-wrap gap-2"><button type="button" class="rounded border border-line px-3 py-1.5 text-sm" @click="openPicker(headerImg)">Choose</button><button type="button" x-show="headerImg.ref" class="rounded border border-line px-3 py-1.5 text-sm" @click="clearImage(headerImg)">Use default</button></div>
                        </div></div>
                </div>
                <div><label class="mb-1 block text-sm font-medium" for="intro">Intro under the title</label><textarea id="intro" name="intro" rows="2" maxlength="400" class="{{ $in }}" @input="dirty = true">{{ old('intro', $page->intro) }}</textarea></div>
            @elseif($page->slug === 'home')
                <p class="text-sm text-muted">The page title is only used here in Admin; the headline visitors see is in the <b>Hero</b> section below. Use the SEO title and meta description on the right for Google.</p>
            @elseif($page->slug !== 'thank-you')
                <div><label class="mb-1 block text-sm font-medium">Main text</label>
                    <input id="body-in" type="hidden" name="body" value="{{ old('body', $page->body) }}"><trix-editor input="body-in" class="trix-content min-h-64 rounded-lg border border-line bg-white"></trix-editor></div>
                @if(in_array($page->slug, ['terms', 'privacy']))<p class="text-sm text-amber-800">Starter template only. Have a qualified professional review it before launch. If the wording materially changes, bump the terms version in config/goaustria.php.</p>@endif
            @endif
        </section>

        {{-- ============ SECTIONS (blocks) ============ --}}
        @if($page->takesBlocks())
        <section class="space-y-3">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <div><h2 class="text-2xl">Sections</h2><p class="text-sm text-muted">{{ $page->slug === 'home' ? 'The home page, top to bottom. Edit, move or hide any section (the hero stays first), and add your own sections below any of them.' : ($custom ? 'The page is built from these sections, top to bottom.' : 'Extra sections shown below the main text.') }}</p></div>
                <p class="text-sm text-muted" x-text="blocks.length + (blocks.length === 1 ? ' section' : ' sections')"></p>
            </div>

            <template x-for="(block, bi) in blocks" :key="block.uid">
                <article class="overflow-hidden rounded-xl border bg-white" :class="block.hidden ? 'border-dashed border-secondary opacity-60' : 'border-line'" :id="'blk-' + block.uid">
                    <header class="flex flex-wrap items-center gap-2 border-b border-line bg-sand/40 px-4 py-3">
                        <span class="grid size-7 shrink-0 place-items-center rounded-full text-xs font-semibold text-white" :class="isFixed(block) ? 'bg-brand-dark' : 'bg-accent-text'" x-text="bi + 1"></span>
                        <button type="button" class="min-w-0 flex-1 text-left" @click="toggle(block)" :aria-expanded="!! open[block.uid]">
                            <span class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-accent-text">
                                <span x-text="types[block.type].label"></span>
                                <span x-show="isFixed(block) && home" class="rounded bg-white px-1.5 py-0.5 text-[10px] tracking-normal text-muted normal-case">built-in</span>
                                <span x-show="block.hidden" class="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] tracking-normal text-amber-900 normal-case">hidden</span>
                            </span>
                            <span class="block truncate text-sm" x-text="summary(block)"></span>
                        </button>
                        <div class="flex flex-wrap items-center gap-1">
                            <button type="button" class="rounded border border-line bg-white px-2 py-1 disabled:opacity-30" @click="move(bi, -1)" :disabled="! canMove(bi, -1)" title="Move up" aria-label="Move up">&uarr;</button>
                            <button type="button" class="rounded border border-line bg-white px-2 py-1 disabled:opacity-30" @click="move(bi, 1)" :disabled="! canMove(bi, 1)" title="Move down" aria-label="Move down">&darr;</button>
                            <button type="button" x-show="! isPinned(block)" class="rounded border border-line bg-white px-2 py-1 text-sm" @click="toggleHidden(block)" x-text="block.hidden ? 'Show' : 'Hide'"></button>
                            <button type="button" x-show="! isFixed(block)" class="rounded border border-line bg-white px-2 py-1 text-sm" @click="duplicate(bi)" title="Duplicate">Copy</button>
                            <button type="button" x-show="! isFixed(block)" class="rounded border border-red-300 bg-white px-2 py-1 text-sm text-red-800" @click="remove(bi)">Remove</button>
                            <button type="button" class="rounded border border-line bg-white px-2 py-1 text-sm" @click="addBelow(bi)" title="Add a section below this one">+ Below</button>
                            <button type="button" class="rounded bg-brand px-3 py-1 text-sm text-white" @click="toggle(block)" x-text="open[block.uid] ? 'Close' : 'Edit'"></button>
                        </div>
                    </header>
                    <div x-show="open[block.uid]" class="space-y-5 p-5">
                        <p class="text-sm text-muted" x-text="types[block.type].hint"></p>
                        <template x-for="[key, f] in Object.entries(types[block.type].fields)" :key="key">
                            <div>
                                <template x-if="f.kind !== 'items'">
                                    @include('admin.pages._field', ['obj' => 'block.data', 'k' => 'key', 'f' => 'f'])
                                </template>
                                <template x-if="f.kind === 'items'">
                                    <fieldset class="space-y-3 rounded-lg border border-line p-4">
                                        <legend class="px-1 text-sm font-medium" x-text="f.label"></legend>
                                        <template x-for="(item, ii) in block.data[key]" :key="ii">
                                            <div class="space-y-3 rounded-lg bg-sand/30 p-4">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-xs font-semibold uppercase tracking-wider text-muted" x-text="'#' + (ii + 1)"></span>
                                                    <div class="flex gap-1">
                                                        <button type="button" class="rounded border border-line bg-white px-2 py-0.5 text-sm" @click="moveItem(block.data[key], ii, -1)" aria-label="Move up">&uarr;</button>
                                                        <button type="button" class="rounded border border-line bg-white px-2 py-0.5 text-sm" @click="moveItem(block.data[key], ii, 1)" aria-label="Move down">&darr;</button>
                                                        <button type="button" class="rounded border border-red-300 bg-white px-2 py-0.5 text-sm text-red-800" @click="removeItem(block.data[key], ii)">Remove</button>
                                                    </div>
                                                </div>
                                                <template x-for="[k, sf] in Object.entries(f.fields)" :key="k">
                                                    @include('admin.pages._field', ['obj' => 'item', 'k' => 'k', 'f' => 'sf'])
                                                </template>
                                            </div>
                                        </template>
                                        <button type="button" class="rounded-full border-2 border-brand px-4 py-1.5 text-sm font-semibold text-brand disabled:opacity-40" @click="addItem(block.data[key], f.fields, f.max_items)" :disabled="block.data[key].length >= f.max_items" x-text="'+ ' + f.add"></button>
                                    </fieldset>
                                </template>
                            </div>
                        </template>
                    </div>
                </article>
            </template>

            <div id="add-panel" class="rounded-xl border border-dashed p-5" :class="insertAt === null ? 'border-secondary bg-white/60' : 'border-cta bg-amber-50'">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-lg" x-text="insertAt === null ? 'Add a section at the end' : 'Add a section below #' + insertAt + ' (' + types[blocks[insertAt - 1]?.type]?.label + ')'"></h3>
                    <button type="button" x-show="insertAt !== null" class="text-sm underline" @click="insertAt = null">Add at the end instead</button>
                </div>
                <div class="grid gap-2 sm:grid-cols-2 2xl:grid-cols-3">
                    <template x-for="[type, t] in addable" :key="type">
                        <button type="button" class="rounded-lg border border-line bg-white p-3 text-left transition hover:border-brand hover:shadow-sm" @click="add(type)">
                            <span class="block font-semibold text-brand" x-text="'+ ' + t.label"></span>
                            <span class="block text-xs text-muted" x-text="t.hint"></span>
                        </button>
                    </template>
                </div>
            </div>
        </section>
        @endif
    </div>

    {{-- ============ SIDEBAR ============ --}}
    <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
        <section class="{{ $card }} space-y-4">
            <h2 class="text-xl">Publish</h2>
            @if($custom)
                <label class="flex items-center gap-3 text-sm"><input type="checkbox" name="is_published" value="1" class="size-5 accent-brand" @checked(old('is_published', $page->is_published)) @change="dirty = true"> Published (visible to visitors)</label>
                <div><label class="mb-1 block text-sm font-medium" for="nav">Show a link in</label>
                    <select id="nav" name="nav_location" class="{{ $in }}" @change="dirty = true">@foreach(\App\Models\Page::NAV_LOCATIONS as $v => $l)<option value="{{ $v }}" @selected(old('nav_location', $page->nav_location ?? '') === $v)>{{ $l }}</option>@endforeach</select></div>
            @else
                <p class="text-sm text-muted">Built-in page: always live at <a class="underline" href="{{ $viewUrl }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://[^/]+#', '', (string) $viewUrl) ?: '/' }}</a>.</p>
            @endif
            <p x-show="dirty" class="text-sm text-amber-800">You have unsaved changes.</p>
            <x-button type="submit" variant="secondary" class="w-full">{{ $isNew ? 'Create page' : 'Save changes' }}</x-button>
        </section>

        <section class="{{ $card }} space-y-4">
            <h2 class="text-xl">Google &amp; sharing</h2>
            <div><label class="mb-1 block text-sm font-medium" for="mt">SEO title</label><input id="mt" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" maxlength="190" placeholder="{{ $page->title ?: 'Page title' }} | {{ \App\Models\Setting::get('brand_name', 'GoAustriaTours') }}" class="{{ $in }}" @input="dirty = true"><p class="mt-1 text-xs text-muted">About 50 to 60 characters. Empty uses the page title.</p></div>
            <div><label class="mb-1 block text-sm font-medium" for="md">Meta description</label><textarea id="md" name="meta_description" rows="3" maxlength="320" class="{{ $in }}" @input="dirty = true">{{ old('meta_description', $page->meta_description) }}</textarea><p class="mt-1 text-xs text-muted">About 150 characters. Empty uses the intro.</p></div>
            <label class="flex items-center gap-3 text-sm"><input type="hidden" name="noindex" value="0"><input type="checkbox" name="noindex" value="1" class="size-5 accent-brand" @checked(old('noindex', $page->noindex)) @change="dirty = true"> Hide from search engines (noindex)</label>
        </section>

        @if($custom && ! $isNew)
        <section class="{{ $card }} space-y-3">
            <h2 class="text-xl">More</h2>
            <button type="submit" form="dup-form" class="w-full rounded border border-line px-3 py-2 text-sm">Duplicate this page</button>
            <button type="submit" form="del-form" class="w-full rounded border border-red-300 px-3 py-2 text-sm text-red-800">Delete this page</button>
        </section>
        @endif
    </aside>

    {{-- ============ PHOTO PICKER ============ --}}
    <div x-show="picker.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-brand-dark/60 p-4" @keydown.escape.window="picker.open = false" @click.self="picker.open = false">
        <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-xl bg-white shadow-xl" role="dialog" aria-modal="true" aria-label="Choose a photo" x-trap.noscroll="picker.open">
            <header class="flex flex-wrap items-center gap-2 border-b border-line p-4">
                <h2 class="mr-auto text-xl">Choose a photo</h2>
                <button type="button" class="rounded-full px-3 py-1 text-sm" :class="picker.tab === 'library' ? 'bg-brand text-white' : 'border border-line'" @click="picker.tab = 'library'" x-text="'Your uploads (' + library.length + ')'"></button>
                <button type="button" class="rounded-full px-3 py-1 text-sm" :class="picker.tab === 'site' ? 'bg-brand text-white' : 'border border-line'" @click="picker.tab = 'site'" x-text="'Site photos (' + site.length + ')'"></button>
                <button type="button" class="rounded-full px-3 py-1 text-sm" :class="picker.tab === 'upload' ? 'bg-brand text-white' : 'border border-line'" @click="picker.tab = 'upload'">Upload new</button>
                <button type="button" class="ml-2 text-2xl leading-none" @click="picker.open = false" aria-label="Close">&times;</button>
            </header>
            <div class="overflow-y-auto p-4">
                <template x-if="picker.tab !== 'upload'">
                    <div>
                        <input type="search" x-model="picker.q" placeholder="Search photos" class="{{ $in }} mb-4">
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                            <template x-for="m in filtered(picker.tab === 'library' ? library : site)" :key="m.ref">
                                <button type="button" class="group overflow-hidden rounded-lg border-2 text-left" :class="picker.target && picker.target.ref === m.ref ? 'border-cta' : 'border-transparent hover:border-brand'" @click="choose(m)">
                                    <img :src="m.thumb" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover">
                                    <span class="block truncate px-2 py-1 text-xs text-muted" x-text="m.label"></span>
                                </button>
                            </template>
                        </div>
                        <p x-show="picker.tab === 'library' && ! library.length" class="py-8 text-center text-sm text-muted">No uploads yet. Use "Upload new" or pick from the site photos.</p>
                    </div>
                </template>
                <template x-if="picker.tab === 'upload'">
                    <div class="max-w-lg space-y-4">
                        <div><label class="mb-1 block text-sm font-medium" for="pk-file">Photo (JPG, PNG or WebP, up to 8 MB)</label><input id="pk-file" type="file" accept="image/jpeg,image/png,image/webp" @change="picker.file = $event.target.files[0]" class="block w-full text-sm"></div>
                        <div><label class="mb-1 block text-sm font-medium" for="pk-alt">Describe the photo (alt text)</label><input id="pk-alt" type="text" x-model="picker.alt" maxlength="190" placeholder="e.g. Hallstatt village and lake in winter" class="{{ $in }}"></div>
                        <p x-show="picker.error" class="text-sm text-red-800" x-text="picker.error"></p>
                        <button type="button" class="rounded-full bg-brand px-6 py-2.5 text-sm font-semibold text-white disabled:opacity-50" @click="upload()" :disabled="picker.busy" x-text="picker.busy ? 'Uploading...' : 'Upload and use'"></button>
                        <p class="text-xs text-muted">Photos are converted to WebP in three sizes and added to Admin &gt; Media.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</form>

@if($custom && ! $isNew)
<form id="dup-form" method="POST" action="{{ route('admin.pages.duplicate', $page) }}">@csrf</form>
<form id="del-form" method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page? This cannot be undone.')">@csrf @method('DELETE')</form>
@endif
@endsection

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Support\Blocks;
use App\Support\HomeSections;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Admin > Pages: create pages from section blocks (App\Support\Blocks), edit them and the built-in pages.
 * Built-in ("system") pages keep their own routes and cannot be deleted; about/terms/privacy also accept blocks below their text.
 * The home page is edited as its built-in sections (App\Support\HomeSections) plus any blocks inserted between them.
 */
class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', [
            'custom' => Page::custom()->orderBy('sort_order')->orderBy('title')->get(),
            'system' => Page::where('is_system', true)->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return $this->form(new Page(['slug' => '', 'title' => '', 'is_system' => false, 'is_published' => false, 'blocks' => [
            Blocks::blank('text'),
        ]]));
    }

    public function store(Request $request)
    {
        $page = new Page(['slug' => '', 'is_system' => false]);
        $this->fill($request, $page);
        $page->save();

        return redirect()->route('admin.pages.edit', $page)->with('status', $page->is_published ? 'Page created and published.' : 'Page created as a draft.');
    }

    public function edit(Page $page)
    {
        return $this->form($page);
    }

    public function update(Request $request, Page $page)
    {
        $this->fill($request, $page);
        $page->save();

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page saved.');
    }

    public function destroy(Page $page)
    {
        abort_if($page->is_system, 403, 'Built-in pages cannot be deleted.');
        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted.');
    }

    public function duplicate(Page $page)
    {
        abort_if($page->is_system, 403);
        $copy = $page->replicate();
        $copy->title = $page->title.' (copy)';
        $copy->slug = $this->uniqueSlug($page->slug.'-copy');
        $copy->is_published = false;
        $copy->save();

        return redirect()->route('admin.pages.edit', $copy)->with('status', 'Copy created as a draft.');
    }

    private function form(Page $page)
    {
        $media = Media::latest('id')->get();
        $home = $page->slug === 'home';
        $types = $home ? Blocks::all() : Blocks::types();
        $blocks = json_decode((string) old('blocks_json', json_encode($page->blocks ?? [])), true) ?: [];

        return view('admin.pages.edit', [
            'page' => $page,
            'builder' => [
                'types' => collect($types)->map(fn ($t, $k) => [
                    'label' => $t['label'], 'hint' => $t['hint'], 'fields' => Blocks::fields($k),
                    'fixed' => $t['fixed'] ?? false, 'pinned' => $t['pinned'] ?? false, 'addable' => ! ($t['home'] ?? false),
                ])->all(),
                'blanks' => collect(array_keys($types))->mapWithKeys(fn ($k) => [$k => Blocks::blank($k)])->all(),
                'blocks' => $home ? HomeSections::normalize(Blocks::sanitize($blocks, true)) : $blocks,
                'home' => $home,
                'header' => (string) old('header_image', $page->header_image),
                'library' => $media->map(fn ($m) => ['ref' => 'media:'.$m->id, 'label' => $m->alt, 'alt' => $m->alt, 'thumb' => asset('storage/'.(collect((array) $m->variants)->sortKeys()->first() ?? $m->path))])->values()->all(),
                'site' => Blocks::siteImages(),
                'uploadUrl' => route('admin.media.store'),
            ],
        ]);
    }

    private function fill(Request $request, Page $page): void
    {
        $custom = ! $page->is_system;
        if ($custom && blank($request->input('slug'))) {
            $request->merge(['slug' => Str::slug((string) $request->input('title'))]);
        }
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => $custom ? ['required', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::notIn(Page::RESERVED), Rule::unique('pages', 'slug')->ignore($page->id)] : ['nullable'],
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'intro' => ['nullable', 'string', 'max:400'],
            'header_image' => ['nullable', 'string', 'max:120'],
            'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'body' => ['nullable', 'string'],
            'nav_location' => ['nullable', Rule::in(array_keys(Page::NAV_LOCATIONS))],
            'blocks_json' => ['nullable', 'string', 'max:500000'],
        ], [
            'slug.not_in' => 'That address is already used by the site. Choose another one.',
            'slug.regex' => 'Use lowercase letters, numbers and dashes only (for example: winter-in-austria).',
            'slug.unique' => 'Another page already uses that address.',
        ]);

        $page->title = $data['title'];
        $page->meta_title = $data['meta_title'] ?? null;
        $page->meta_description = $data['meta_description'] ?? null;
        $page->noindex = $request->boolean('noindex');

        if ($custom) {
            $page->slug = $data['slug'];
            $page->eyebrow = $data['eyebrow'] ?? null;
            $page->intro = $data['intro'] ?? null;
            $header = Blocks::sanitize([['type' => 'image_text', 'data' => ['image' => ['ref' => $data['header_image'] ?? '']]]])[0]['data']['image']['ref'] ?? '';
            $page->header_image = $header !== '' ? $header : null;
            $page->is_published = $request->boolean('is_published');
            $page->nav_location = ($data['nav_location'] ?? '') !== '' ? $data['nav_location'] : null;
        } elseif (! in_array($page->slug, ['home', 'thank-you'], true)) {
            $page->body = isset($data['body']) ? $this->cleanBody($data['body']) : null;
        }

        if ($page->slug === 'home') {
            $page->blocks = HomeSections::normalize(Blocks::sanitize(json_decode((string) ($data['blocks_json'] ?? '[]'), true), true));
        } elseif ($page->takesBlocks()) {
            $page->blocks = Blocks::sanitize(json_decode((string) ($data['blocks_json'] ?? '[]'), true));
        }
    }

    /** Built-in page body (Trix): keeps its formatting, removes scripts and event handlers. */
    private function cleanBody(string $html): string
    {
        $html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', $html);

        return preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base;
        for ($n = 2; Page::where('slug', $slug)->exists() || in_array($slug, Page::RESERVED, true); $n++) {
            $slug = $base.'-'.$n;
        }

        return $slug;
    }
}

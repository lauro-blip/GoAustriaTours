<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /** Home page text blocks editable in admin: key => label. */
    public const HOME_SECTIONS = [
        'hero_title' => 'Hero headline (H1)', 'hero_subtitle' => 'Hero subheading',
        'why_title' => '"Why choose us" heading', 'cta_title' => 'Final call to action heading', 'cta_text' => 'Final call to action text',
    ];

    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('id')->get()]);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', ['page' => $page, 'homeSections' => self::HOME_SECTIONS]);
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'], 'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:320'], 'body' => ['nullable', 'string'],
            'sections' => ['nullable', 'array'], 'sections.*' => ['nullable', 'string', 'max:2000'],
        ]);
        $data['noindex'] = $request->boolean('noindex');
        $data['body'] = isset($data['body']) ? $this->clean($data['body']) : null;
        if ($page->slug === 'home') {
            $data['sections'] = array_merge($page->sections ?? [], array_map('strip_tags', $data['sections'] ?? []));
        } else {
            unset($data['sections']);
        }
        $page->update($data);

        return redirect()->route('admin.pages.index')->with('status', 'Page saved.');
    }

    private function clean(string $html): string
    {
        $html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', $html);

        return preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
    }
}

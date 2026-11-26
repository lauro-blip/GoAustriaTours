<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PageController as PublicPages;
use App\Models\Page;
use App\Models\TourType;
use App\Support\Blocks;
use App\Support\HomeSections;
use Illuminate\Http\Request;

/**
 * Admin > Pages visual editor: renders a page with UNSAVED changes using the real public templates, plus a small
 * click-to-select / inline-editing layer (admin/pages/_preview-overlay). Nothing is written to the database.
 */
class PagePreviewController extends Controller
{
    public function show(Request $request, ?Page $page = null)
    {
        $request->validate(['blocks_json' => ['nullable', 'string', 'max:500000'], 'title' => ['nullable', 'string', 'max:190']]);
        $p = $page ? $page->replicate() : new Page(['slug' => 'preview', 'is_system' => false]);
        $p->id = $page?->id;
        $p->title = (string) $request->input('title', $p->title ?? '');
        $p->is_published = true;
        $home = $p->slug === 'home';

        if (! $p->is_system) {
            $p->eyebrow = $request->input('eyebrow');
            $p->intro = $request->input('intro');
            $ref = Blocks::sanitize([['type' => 'image_text', 'data' => ['image' => ['ref' => (string) $request->input('header_image', '')]]]])[0]['data']['image']['ref'] ?? '';
            $p->header_image = $ref !== '' ? $ref : null;
        }
        if ($request->has('body') && ! $home) {
            $p->body = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', (string) $request->input('body'));
        }
        $raw = json_decode((string) $request->input('blocks_json', '[]'), true);
        $p->blocks = $home ? HomeSections::normalize(Blocks::sanitize($raw, true)) : Blocks::sanitize($raw);

        // "editing" reaches blocks/_all through the includes (no global share, so nothing leaks into other renders).
        $html = match (true) {
            $home => view('pages.home', app(PublicPages::class)->homeData($p) + ['editing' => true])->render(),
            ! $p->is_system => view('pages.custom', ['page' => $p, 'editing' => true])->render(),
            default => view('pages.simple', ['page' => $p, 'tourTypes' => TourType::active()->get(), 'editing' => true])->render(),
        };
        $overlay = view('admin.pages._preview-overlay', ['labels' => collect(Blocks::all())->map(fn ($t) => $t['label'])->all()])->render();

        return response(str_replace('</body>', $overlay.'</body>', $html))->header('X-Robots-Tag', 'noindex');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\ImageService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class MediaController extends Controller
{
    public function index()
    {
        return view('admin.media', ['items' => Media::latest('id')->paginate(24)]);
    }

    public function store(Request $request, ImageService $images)
    {
        $request->validate(['image' => ['required', 'image', 'max:8192'], 'alt' => ['required', 'string', 'max:190']], ['alt.required' => 'Alt text is required.']);
        try {
            $media = $images->storeMedia($request->file('image'), $request->alt);
        } catch (InvalidArgumentException $e) {
            return $request->wantsJson() ? response()->json(['message' => $e->getMessage()], 422) : back()->withErrors(['image' => $e->getMessage()]);
        }

        // The page builder's photo picker uploads in the background and selects the new photo straight away.
        if ($request->wantsJson()) {
            return response()->json([
                'ref' => 'media:'.$media->id, 'label' => $media->alt, 'alt' => $media->alt,
                'thumb' => asset('storage/'.(collect((array) $media->variants)->sortKeys()->first() ?? $media->path)),
            ]);
        }

        return back()->with('status', 'Uploaded and converted to WebP.');
    }

    public function destroy(Media $media, ImageService $images)
    {
        foreach ((array) $media->variants as $path) {
            $images->delete($path);
        }
        $media->delete();

        return back()->with('status', 'Deleted.');
    }
}

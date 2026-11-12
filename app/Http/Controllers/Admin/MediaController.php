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
            $images->storeMedia($request->file('image'), $request->alt);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['image' => $e->getMessage()]);
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

<?php

namespace App\Http\Controllers;

use App\Models\Post;

class JournalController extends Controller
{
    public function index()
    {
        return view('journal.index', ['posts' => Post::published()->paginate(9)]);
    }

    public function show(Post $post)
    {
        abort_unless($post->is_published && $post->published_at?->lte(now()), 404);

        return view('journal.show', [
            'post' => $post,
            'more' => Post::published()->where('id', '!=', $post->id)->take(3)->get(),
        ]);
    }
}

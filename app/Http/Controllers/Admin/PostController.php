<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PostController extends CrudController
{
    protected function model(): string { return Post::class; }
    protected function routeName(): string { return 'admin.posts'; }
    protected function title(): string { return 'Journal posts'; }
    protected function columns(): array { return ['Title' => 'title', 'Category' => 'category', 'Published' => 'is_published']; }
    protected function slugSource(): string { return 'title'; }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
            ['name' => 'slug', 'label' => 'URL slug (leave blank to generate)', 'type' => 'text'],
            ['name' => 'category', 'label' => 'Category (e.g. Travel tips)', 'type' => 'text'],
            ['name' => 'excerpt', 'label' => 'Short summary (shown on cards)', 'type' => 'textarea'],
            ['name' => 'body', 'label' => 'Article', 'type' => 'richtext'],
            ['name' => 'image', 'label' => 'Image', 'type' => 'image'],
            ['name' => 'image_alt', 'label' => 'Image alt text', 'type' => 'text'],
            ['name' => 'meta_title', 'label' => 'SEO title', 'type' => 'text'],
            ['name' => 'meta_description', 'label' => 'Meta description', 'type' => 'textarea'],
            ['name' => 'is_published', 'label' => 'Published (visible on the site)', 'type' => 'checkbox'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return [
            'title' => ['required', 'string', 'max:190'], 'slug' => ['nullable', 'string', 'max:190'],
            'category' => ['nullable', 'string', 'max:80'], 'excerpt' => ['nullable', 'string', 'max:600'],
            'body' => ['nullable', 'string'], 'image' => ['nullable', 'image', 'max:8192'],
            'image_alt' => ['nullable', 'string', 'max:190'], 'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:320'], 'is_published' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        // Stamp the publish date the first time a post goes live.
        if (! empty($data['is_published']) && ! $item->published_at) {
            $data['published_at'] = now();
        }

        return $data;
    }
}

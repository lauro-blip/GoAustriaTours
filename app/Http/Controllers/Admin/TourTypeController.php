<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourType;
use Illuminate\Database\Eloquent\Model;

class TourTypeController extends CrudController
{
    protected function model(): string { return TourType::class; }
    protected function routeName(): string { return 'admin.tour-types'; }
    protected function title(): string { return 'Tour types'; }
    protected function columns(): array { return ['Name' => 'name', 'Slug' => 'slug', 'Active' => 'is_active']; }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'slug', 'label' => 'URL slug (leave blank to generate)', 'type' => 'text'],
            ['name' => 'tagline', 'label' => 'Tagline', 'type' => 'text'],
            ['name' => 'summary', 'label' => 'Summary', 'type' => 'textarea'],
            ['name' => 'body', 'label' => 'Page content', 'type' => 'richtext'],
            ['name' => 'image', 'label' => 'Image', 'type' => 'image'],
            ['name' => 'image_alt', 'label' => 'Image alt text', 'type' => 'text'],
            ['name' => 'meta_title', 'label' => 'SEO title', 'type' => 'text'],
            ['name' => 'meta_description', 'label' => 'Meta description', 'type' => 'textarea'],
            ['name' => 'is_active', 'label' => 'Active (shown on the site)', 'type' => 'checkbox'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return [
            'name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:140'],
            'tagline' => ['nullable', 'string', 'max:190'], 'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'], 'image' => ['nullable', 'image', 'max:8192'],
            'image_alt' => ['nullable', 'string', 'max:190'], 'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:320'], 'is_active' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, \Illuminate\Http\Request $request, Model $item): array
    {
        if (! $item->exists) {
            $data['sort_order'] = TourType::max('sort_order') + 1;
        }

        return $data;
    }
}

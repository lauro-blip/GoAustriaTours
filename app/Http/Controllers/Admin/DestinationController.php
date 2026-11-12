<?php

namespace App\Http\Controllers\Admin;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DestinationController extends CrudController
{
    protected function model(): string { return Destination::class; }
    protected function routeName(): string { return 'admin.destinations'; }
    protected function title(): string { return 'Destinations'; }
    protected function columns(): array { return ['Name' => 'name', 'Slug' => 'slug', 'Active' => 'is_active']; }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'slug', 'label' => 'URL slug (leave blank to generate)', 'type' => 'text'],
            ['name' => 'summary', 'label' => 'Summary', 'type' => 'textarea'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return ['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string', 'max:140'], 'summary' => ['nullable', 'string', 'max:1000'], 'is_active' => ['boolean']];
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        if (! $item->exists) {
            $data['sort_order'] = Destination::max('sort_order') + 1;
        }

        return $data;
    }
}

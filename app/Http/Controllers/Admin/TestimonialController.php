<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TestimonialController extends CrudController
{
    protected function model(): string { return Testimonial::class; }
    protected function routeName(): string { return 'admin.testimonials'; }
    protected function title(): string { return 'Testimonials'; }
    protected function columns(): array { return ['Name' => 'name', 'Quote' => 'quote', 'Placeholder' => 'is_placeholder', 'Active' => 'is_active']; }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Guest name', 'type' => 'text'],
            ['name' => 'location', 'label' => 'Location', 'type' => 'text'],
            ['name' => 'quote', 'label' => 'Review text', 'type' => 'textarea'],
            ['name' => 'is_placeholder', 'label' => 'This is placeholder text (untick once it is a real, permitted review)', 'type' => 'checkbox'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return ['name' => ['required', 'string', 'max:120'], 'location' => ['nullable', 'string', 'max:120'], 'quote' => ['required', 'string', 'max:2000'], 'is_placeholder' => ['boolean'], 'is_active' => ['boolean']];
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        if (! $item->exists) {
            $data['sort_order'] = Testimonial::max('sort_order') + 1;
        }

        return $data;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FaqController extends CrudController
{
    protected function model(): string { return Faq::class; }
    protected function routeName(): string { return 'admin.faqs'; }
    protected function title(): string { return 'Home page FAQs'; }
    protected function columns(): array { return ['Question' => 'question', 'Active' => 'is_active']; }

    protected function fields(): array
    {
        return [
            ['name' => 'question', 'label' => 'Question', 'type' => 'text'],
            ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return ['question' => ['required', 'string', 'max:255'], 'answer' => ['required', 'string', 'max:3000'], 'is_active' => ['boolean']];
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        if (! $item->exists) {
            $data['sort_order'] = Faq::max('sort_order') + 1;
        }

        return $data;
    }
}

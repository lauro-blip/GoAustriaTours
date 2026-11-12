<?php

namespace App\Http\Controllers\Admin;

use App\Models\QuizStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Quiz steps (CRUD + reorder) plus an options editor per step. */
class QuizController extends CrudController
{
    protected function model(): string { return QuizStep::class; }
    protected function routeName(): string { return 'admin.quiz'; }
    protected function title(): string { return 'Hero quiz steps'; }
    protected function columns(): array { return ['Question' => 'question', 'Type' => 'type', 'Key' => 'key', 'Active' => 'is_active']; }

    protected function extraActions(): array
    {
        return [['label' => 'Options', 'route' => 'admin.quiz.options']];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'question', 'label' => 'Question', 'type' => 'text'],
            ['name' => 'key', 'label' => 'Answer key (short, lowercase, e.g. season; used as the CRM field name)', 'type' => 'text'],
            ['name' => 'type', 'label' => 'Answer style', 'type' => 'select', 'options' => ['tiles' => 'Tiles (advance on click)', 'dropdown' => 'Dropdown + Next button']],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox'],
        ];
    }

    protected function rules(?Model $item): array
    {
        return [
            'question' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:40', 'regex:/^[a-z0-9_]+$/'],
            'type' => ['required', Rule::in(['tiles', 'dropdown'])],
            'is_active' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, Request $request, Model $item): array
    {
        if (! $item->exists) {
            $data['sort_order'] = QuizStep::max('sort_order') + 1;
        }

        return $data;
    }

    public function options(int $id)
    {
        return view('admin.quiz.options', ['step' => QuizStep::with('options')->findOrFail($id), 'icons' => $this->iconNames()]);
    }

    public function saveOption(Request $request, int $id, ?int $optionId = null)
    {
        $step = QuizStep::findOrFail($id);
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'], 'value' => ['required', 'string', 'max:60'], 'icon' => ['nullable', 'string', 'max:30'],
        ]) + ['is_active' => $request->boolean('is_active')];

        if ($optionId) {
            $step->options()->findOrFail($optionId)->update($data);
        } else {
            $step->options()->create($data + ['sort_order' => ($step->options()->max('sort_order') ?? 0) + 1]);
        }

        return back()->with('status', 'Saved.');
    }

    public function deleteOption(int $id, int $optionId)
    {
        QuizStep::findOrFail($id)->options()->findOrFail($optionId)->delete();

        return back()->with('status', 'Option removed.');
    }

    public function moveOption(int $id, int $optionId, string $dir)
    {
        $ids = QuizStep::findOrFail($id)->options()->pluck('id')->all();
        $i = array_search($optionId, $ids, true);
        $j = $dir === 'up' ? $i - 1 : $i + 1;
        if ($i !== false && isset($ids[$j])) {
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $n => $key) {
                \App\Models\QuizOption::whereKey($key)->update(['sort_order' => $n + 1]);
            }
        }

        return back();
    }

    private function iconNames(): array
    {
        return ['city', 'mountain', 'flower', 'sun', 'leaf', 'snow', 'bus', 'car', 'guide', 'question', 'check', 'cross', 'coin', 'search', 'star', 'pin', 'shield'];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizStep extends Model
{
    protected $table = 'quiz_steps';
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class)->orderBy('sort_order');
    }

    /** Active steps with active options, as a plain array for the quiz component. */
    public static function forQuiz(): array
    {
        return static::where('is_active', true)->orderBy('sort_order')->with(['options' => fn ($q) => $q->where('is_active', true)])
            ->get()->map(fn ($s) => [
                'key' => $s->key, 'question' => $s->question, 'type' => $s->type,
                'options' => $s->options->map(fn ($o) => ['label' => $o->label, 'value' => $o->value, 'icon' => $o->icon])->values()->all(),
            ])->all();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizOption extends Model
{
    protected $table = 'quiz_options';
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];
}

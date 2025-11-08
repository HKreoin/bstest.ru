<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_active',
        'time_limit_minutes',
        'questions_per_attempt',
        'passing_score_percent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'time_limit_minutes' => 'integer',
        'questions_per_attempt' => 'integer',
        'passing_score_percent' => 'integer',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function drawQuestionsForAttempt(): Collection
    {
        $limit = $this->questions_per_attempt;

        $query = Question::query()
            ->where('test_id', $this->id)
            ->with('answerOptions')
            ->inRandomOrder();

        if ($limit && $limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }
}

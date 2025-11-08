<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Question extends Model
{
    use HasFactory;

    public const TYPE_SINGLE = 'single_choice';
    public const TYPE_MULTIPLE = 'multiple_choice';
    protected $fillable = [
        'test_id',
        'text',
        'type',
        'points',
        'position',
    ];

    protected $casts = [
        'points' => 'integer',
        'position' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answerOptions(): HasMany
    {
        return $this->hasMany(AnswerOption::class);
    }

    public function questionAttempts(): HasMany
    {
        return $this->hasMany(QuestionAttempt::class);
    }

    public function correctOptionIds(): Collection
    {
        if ($this->relationLoaded('answerOptions')) {
            return $this->answerOptions
                ->where('is_correct', true)
                ->pluck('id')
                ->values();
        }

        return $this->answerOptions()
            ->where('is_correct', true)
            ->pluck('id');
    }

    public function isSingleChoice(): bool
    {
        return $this->type === self::TYPE_SINGLE;
    }

    public function isMultipleChoice(): bool
    {
        return $this->type === self::TYPE_MULTIPLE;
    }
}

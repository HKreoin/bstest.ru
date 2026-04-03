<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestAttempt extends Model
{
    use HasFactory;

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'test_id',
        'participant_name',
        'participant_email',
        'personal_data_consent_at',
        'status',
        'total_questions',
        'correct_questions',
        'score_percent',
        'passed',
        'started_at',
        'completed_at',
        'time_spent_seconds',
    ];

    protected $attributes = [
        'status' => self::STATUS_IN_PROGRESS,
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'correct_questions' => 'integer',
        'score_percent' => 'decimal:2',
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'personal_data_consent_at' => 'datetime',
        'time_spent_seconds' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function questionAttempts(): HasMany
    {
        return $this->hasMany(QuestionAttempt::class)->orderBy('id');
    }

    public function markCompleted(
        int $correctCount,
        int $totalCount,
        ?float $scorePercent,
        bool $passed,
        ?int $timeSpentSeconds = null,
    ): void {
        $this->forceFill([
            'status' => self::STATUS_COMPLETED,
            'correct_questions' => $correctCount,
            'total_questions' => $totalCount,
            'score_percent' => $scorePercent,
            'passed' => $passed,
            'completed_at' => now(),
            'time_spent_seconds' => $timeSpentSeconds,
        ])->save();
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function calculatedTimeSpentSeconds(): ?int
    {
        if ($this->time_spent_seconds !== null) {
            return $this->time_spent_seconds;
        }

        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInSeconds($this->completed_at);
        }

        return null;
    }
}

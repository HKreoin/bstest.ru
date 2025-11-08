<?php

namespace App\Models;

use App\Models\AnswerOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class QuestionAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_attempt_id',
        'question_id',
        'selected_option_ids',
        'text_answer',
        'is_correct',
        'points_awarded',
    ];

    protected $casts = [
        'selected_option_ids' => 'array',
        'is_correct' => 'boolean',
        'points_awarded' => 'integer',
    ];

    protected $appends = [
        'selected_option_text',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function getSelectedOptionTextAttribute(): string
    {
        $ids = $this->selected_option_ids;

        if (! is_array($ids) || empty($ids)) {
            return '—';
        }

        $ids = array_map('intval', $ids);

        $options = $this->getLoadedAnswerOptions()
            ->whereIn('id', $ids)
            ->pluck('text')
            ->unique()
            ->values();

        if ($options->isEmpty()) {
            $options = AnswerOption::query()
                ->whereIn('id', $ids)
                ->pluck('text')
                ->unique()
                ->values();
        }

        return $options->isNotEmpty() ? $options->implode(', ') : '—';
    }

    protected function getLoadedAnswerOptions(): Collection
    {
        if (
            $this->relationLoaded('question')
            && $this->question
            && $this->question->relationLoaded('answerOptions')
        ) {
            return $this->question->answerOptions;
        }

        return collect();
    }
}

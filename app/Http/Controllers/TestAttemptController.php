<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitTestAttemptRequest;
use App\Models\TestAttempt;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestAttemptController extends Controller
{
    public function show(Request $request, TestAttempt $testAttempt): View
    {
        abort_unless($this->canAccessAttempt($request, $testAttempt), 403);

        $testAttempt->load([
            'test',
            'questionAttempts.question.answerOptions',
        ]);

        if ($testAttempt->isCompleted()) {
            return view('tests.result', [
                'attempt' => $testAttempt,
            ]);
        }

        return view('tests.attempt', [
            'attempt' => $testAttempt,
        ]);
    }

    public function submit(SubmitTestAttemptRequest $request, TestAttempt $testAttempt): RedirectResponse
    {
        abort_unless($this->canAccessAttempt($request, $testAttempt), 403);
        abort_if($testAttempt->isCompleted(), 400, 'Попытка уже завершена.');

        $answers = $request->validated('answers');

        DB::transaction(function () use ($answers, $testAttempt): void {
            $testAttempt->loadMissing([
                'test',
                'questionAttempts.question.answerOptions',
            ]);

            $correctQuestions = 0;
            $earnedPoints = 0;
            $totalPoints = 0;

            foreach ($testAttempt->questionAttempts as $questionAttempt) {
                $question = $questionAttempt->question;
                $totalPoints += $question->points;

                $input = $answers[$questionAttempt->id] ?? null;

                $selectedIds = (match (true) {
                    is_array($input) => collect($input),
                    is_null($input) => collect(),
                    default => collect([$input]),
                })
                    ->map(fn ($value) => (int) $value)
                    ->filter()
                    ->unique()
                    ->values();

                if ($question->isSingleChoice() && $selectedIds->isNotEmpty()) {
                    $selectedIds = collect([$selectedIds->first()]);
                }

                $correctIds = $question->correctOptionIds()
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values();

                $selectedIdsSorted = $selectedIds->sort()->values();

                $isCorrect = $selectedIdsSorted->isNotEmpty()
                    && $selectedIdsSorted->count() === $correctIds->count()
                    && $selectedIdsSorted->every(fn ($id, $index) => $id === $correctIds->get($index));

                $pointsAwarded = $isCorrect ? $question->points : 0;

                if ($isCorrect) {
                    $correctQuestions++;
                    $earnedPoints += $pointsAwarded;
                }

                $questionAttempt->update([
                    'selected_option_ids' => $selectedIds->values()->all(),
                    'text_answer' => null,
                    'is_correct' => $isCorrect,
                    'points_awarded' => $pointsAwarded,
                ]);
            }

            $scorePercent = $totalPoints > 0
                ? round(($earnedPoints / $totalPoints) * 100, 2)
                : null;

            $passed = $scorePercent !== null
                && $scorePercent >= $testAttempt->test->passing_score_percent;

            $timeSpent = $testAttempt->started_at
                ? $testAttempt->started_at->diffInSeconds(now())
                : null;

            $testAttempt->markCompleted(
                correctCount: $correctQuestions,
                totalCount: $testAttempt->questionAttempts->count(),
                scorePercent: $scorePercent,
                passed: $passed,
                timeSpentSeconds: $timeSpent,
            );
        });

        $this->updateSessionAttempt($request, $testAttempt);

        return redirect()->route('attempts.show', $testAttempt)->with('status', 'Тест завершён.');
    }

    protected function canAccessAttempt(Request $request, TestAttempt $attempt): bool
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return true;
        }

        return collect($request->session()->get('allowed_attempt_ids', []))
            ->contains($attempt->id);
    }

    protected function updateSessionAttempt(Request $request, TestAttempt $attempt): void
    {
        $attempts = collect($request->session()->get('test_attempts', []))
            ->map(function (array $item) use ($attempt) {
                if ($item['id'] !== $attempt->id) {
                    return $item;
                }

                $item['score_percent'] = $attempt->score_percent;
                $item['passed'] = $attempt->passed;
                $item['completed_at'] = optional($attempt->completed_at)->timestamp;

                return $item;
            })
            ->values()
            ->all();

        $request->session()->put('test_attempts', $attempts);
    }

}



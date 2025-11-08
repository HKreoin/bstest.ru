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
            if ($request->boolean('print')) {
                return view('tests.print', [
                    'attempt' => $testAttempt,
                ]);
            }

            return view('tests.result', [
                'attempt' => $testAttempt,
            ]);
        }

        return view('tests.attempt', [
            'attempt' => $testAttempt,
        ]);
    }

    public function downloadProtocol(Request $request, TestAttempt $testAttempt)
    {
        abort_unless($this->canAccessAttempt($request, $testAttempt), 403);

        $testAttempt->load([
            'test',
            'questionAttempts.question.answerOptions',
        ]);

        abort_unless($testAttempt->isCompleted(), 404);

        $content = $this->buildRtfProtocol($testAttempt);

        $filename = sprintf(
            'protocol-%s-%s.rtf',
            $testAttempt->test->slug ?? $testAttempt->test_id,
            $testAttempt->id
        );

        return response($content)
            ->withHeaders([
                'Content-Type' => 'application/rtf; charset=windows-1251',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
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

    protected function buildRtfProtocol(TestAttempt $attempt): string
    {
        $completedAt = optional($attempt->completed_at)->format('d.m.Y H:i');
        $timeSpent = $attempt->time_spent_seconds ? gmdate('H:i:s', $attempt->time_spent_seconds) : '—';
        $scorePercent = $attempt->score_percent !== null ? number_format((float) $attempt->score_percent, 2) . ' %' : '—';

        $fullName = trim($attempt->participant_name ?? '');
        $parts = preg_split('/\s+/u', $fullName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $lastName = $parts[0] ?? '';
        $initials = '';

        if (count($parts) > 1) {
            $initialsArray = array_slice($parts, 1);
            $initials = implode(' ', array_map(static fn ($part) => mb_substr($part, 0, 1) . '.', $initialsArray));
        }

        $signatureName = trim($lastName . ' ' . $initials);

        $lines = [
            '{\rtf1\ansi\ansicpg1251\deff0',
            '{\fonttbl{\f0 Arial;}}',
            '\viewkind4\uc1',
            '\pard\qc\f0\fs28 ' . $this->rtfText('Протокол прохождения теста') . '\par',
            '\pard\qc\f0\fs24 ' . $this->rtfText('«'.$attempt->test->title.'»') . '\par',
            '\pard\par',
            '\pard\sa160\f0\fs20 ' . $this->rtfText('Участник: '.$attempt->participant_name) . '\par',
        ];

        if ($attempt->participant_email) {
            $lines[] = '\pard\sa120\f0\fs20 ' . $this->rtfText('Email: '.$attempt->participant_email) . '\par';
        }

        $lines = array_merge($lines, [
            '\pard\sa120\f0\fs20 ' . $this->rtfText('Дата завершения: '.$completedAt) . '\par',
            '\pard\sa120\f0\fs20 ' . $this->rtfText('Время прохождения: '.$timeSpent) . '\par',
            '\pard\sa120\f0\fs20 ' . $this->rtfText('Результат: '.$scorePercent.' (проходной минимум '.$attempt->test->passing_score_percent.' %)') . '\par',
            '\pard\sa240\f0\fs20 ' . $this->rtfText('Итог: '.($attempt->passed ? 'ТЕСТ ПРОЙДЕН' : 'ТЕСТ НЕ ПРОЙДЕН')) . '\par',
            '\pard\sa200\f0\fs20 ' . $this->rtfText('Правильных ответов: '.$attempt->correct_questions.' из '.$attempt->total_questions) . '\par',
            '\pard\sa240\par',
        ]);

        foreach ($attempt->questionAttempts as $index => $questionAttempt) {
            $lines[] = '\pard\sa140\f0\fs20 ' . $this->rtfText(($index + 1).'. '.$questionAttempt->question->text) . '\par';
            $lines[] = '\pard\fi360\sa100\f0\fs18 ' . $this->rtfText('Ответы: '.$questionAttempt->selected_option_text) . '\par';
            $lines[] = '\pard\fi360\sa200\f0\fs18 ' . $this->rtfText(sprintf(
                'Баллы: %d / %d. Статус: %s',
                $questionAttempt->points_awarded,
                $questionAttempt->question->points,
                $questionAttempt->is_correct ? 'Верно' : 'Неверно'
            )) . '\par';
            $lines[] = '\pard\sa180\par';
        }

        $lines = array_merge($lines, [
            '\pard\sa280\f0\fs18 ' . $this->rtfText('Подпись участника: ____________________ ' . ($signatureName !== '' ? '(' . $signatureName . ')' : '')) . '\par',
            '\pard\sa120\f0\fs18 ' . $this->rtfText('Дата: ' . $completedAt) . '\par',
            '}',
        ]);

        $rtf = implode("\n", $lines);
        $converted = iconv('UTF-8', 'CP1251//TRANSLIT', $rtf);

        return $converted !== false ? $converted : $rtf;
    }

    protected function rtfText(string $text): string
    {
        $escaped = str_replace(
            ['\\', '{', '}'],
            ['\\\\', '\{', '\}'],
            $text,
        );

        return preg_replace("/(\r\n|\r|\n)/", '\\par ', $escaped);
    }
}



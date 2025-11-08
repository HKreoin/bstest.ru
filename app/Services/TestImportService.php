<?php

namespace App\Services;

use App\Models\AnswerOption;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class TestImportService
{
    /**
     * @throws \Throwable
     */
    public function importFromPath(Test $test, string $path): int
    {
        if (! File::exists($path)) {
            throw new RuntimeException('Файл не найден.');
        }

        $content = File::get($path);

        if ($content === false) {
            throw new RuntimeException('Не удалось прочитать файл.');
        }

        $questionsData = $this->parseContent($content);

        if ($questionsData->isEmpty()) {
            throw new RuntimeException('Подходящих вопросов в файле не найдено.');
        }

        return DB::transaction(function () use ($test, $questionsData): int {
            $position = (int) ($test->questions()->max('position') ?? 0);
            $createdCount = 0;

            foreach ($questionsData as $questionData) {
                $position++;

                $question = Question::create([
                    'test_id' => $test->id,
                    'text' => $questionData['question'],
                    'type' => $questionData['type'],
                    'points' => $questionData['points'],
                    'position' => $position,
                ]);

                foreach ($questionData['answers'] as $index => $answer) {
                    AnswerOption::create([
                        'question_id' => $question->id,
                        'text' => $answer['text'],
                        'is_correct' => $answer['is_correct'],
                    ]);
                }

                $createdCount++;
            }

            return $createdCount;
        });
    }

    protected function parseContent(string $content): Collection
    {
        $lines = preg_split('/\r\n|\r|\n/', $content);

        if (! $lines) {
            return collect();
        }

        $questions = collect();
        $currentQuestion = null;
        $waitingForAnswer = false;
        $currentAnswerCorrect = false;

        foreach ($lines as $line) {
            $line = rtrim($line, "\r\n");
            $trimmed = trim($line);

            if ($trimmed === '') {
                continue;
            }

            if ($trimmed === '?') {
                if ($currentQuestion !== null) {
                    $questions->push($this->finalizeQuestion($currentQuestion));
                }

                $currentQuestion = [
                    'question_lines' => [],
                    'answers' => [],
                ];

                $waitingForAnswer = false;
                $currentAnswerCorrect = false;

                continue;
            }

            if (preg_match('/^\d+[\.\)]/', $trimmed)) {
                if (
                    $currentQuestion !== null
                    && (! empty($currentQuestion['question_lines']) || ! empty($currentQuestion['answers']))
                ) {
                    $questions->push($this->finalizeQuestion($currentQuestion));
                    $currentQuestion = null;
                }

                if ($currentQuestion === null) {
                    $currentQuestion = [
                        'question_lines' => [],
                        'answers' => [],
                    ];
                }

                $currentQuestion['question_lines'][] = $line;
                $waitingForAnswer = false;
                $currentAnswerCorrect = false;

                continue;
            }

            if ($trimmed === '+') {
                $waitingForAnswer = true;
                $currentAnswerCorrect = true;

                continue;
            }

            if ($trimmed === '-') {
                $waitingForAnswer = true;
                $currentAnswerCorrect = false;

                continue;
            }

            if ($waitingForAnswer && $currentQuestion !== null) {
                $answerText = trim($line);

                if ($answerText !== '') {
                    $currentQuestion['answers'][] = [
                        'text' => $answerText,
                        'is_correct' => $currentAnswerCorrect,
                    ];
                }

                $waitingForAnswer = false;
                $currentAnswerCorrect = false;

                continue;
            }

            if ($currentQuestion !== null) {
                if ($trimmed !== '') {
                    $currentQuestion['question_lines'][] = $line;
                }
            }
        }

        if ($currentQuestion !== null) {
            $questions->push($this->finalizeQuestion($currentQuestion));
        }

        return $questions
            ->filter()
            ->values();
    }

    protected function finalizeQuestion(array $questionData): ?array
    {
        $questionText = trim(implode("\n", $questionData['question_lines'] ?? []));
        $answers = collect($questionData['answers'] ?? [])
            ->filter(fn (array $answer) => trim($answer['text']) !== '')
            ->values();

        if ($questionText === '' || $answers->isEmpty()) {
            return null;
        }

        $correctCount = $answers->where('is_correct', true)->count();
        $type = $correctCount === 1 ? Question::TYPE_SINGLE : Question::TYPE_MULTIPLE;

        if ($correctCount === 0) {
            $answers = $answers
                ->values()
                ->map(function (array $answer, int $index): array {
                    $answer['is_correct'] = $index === 0;

                    return $answer;
                });

            $type = Question::TYPE_SINGLE;
        }

        return [
            'question' => $questionText,
            'answers' => $answers->toArray(),
            'type' => $type,
            'points' => 1,
        ];
    }
}



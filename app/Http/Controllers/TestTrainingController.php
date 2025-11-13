<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Test;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TestTrainingController extends Controller
{
    public function configure(Test $test): View
    {
        $test->loadCount('questions');

        return view('tests.training.configure', [
            'test' => $test,
        ]);
    }

    public function start(Request $request, Test $test): RedirectResponse
    {
        $totalQuestions = (int) $test->questions()->count();

        abort_if($totalQuestions === 0, 404);

        $data = $request->validate([
            'question_count' => ['required', 'integer', 'min:1', 'max:'.$totalQuestions],
            'order' => ['required', 'in:original,random'],
        ]);

        $questions = $test->questions()
            ->with('answerOptions')
            ->orderBy('position')
            ->get();

        if ($data['order'] === 'random') {
            $questions = $questions->shuffle();
        }

        $selectedQuestions = $questions->take($data['question_count'])->values();

        $sessionId = (string) Str::uuid();

        $request->session()->put('training_sessions.'.$sessionId, [
            'test_id' => $test->id,
            'test_title' => $test->title,
            'questions' => $selectedQuestions->map(fn (Question $question) => [
                'id' => $question->id,
                'text' => $question->text,
                'type' => $question->type,
                'points' => $question->points,
                'answers' => $question->answerOptions
                    ->map(fn ($option) => [
                        'id' => (int) $option->id,
                        'text' => $option->text,
                        'is_correct' => (bool) $option->is_correct,
                    ])
                    ->toArray(),
            ])->toArray(),
        ]);

        return redirect()->route('tests.training.attempt', [
            'test' => $test,
            'session' => $sessionId,
        ]);
    }

    public function attempt(Request $request, Test $test, string $session): View
    {
        $sessionData = $this->resolveSession($request, $test, $session);

        return view('tests.training.attempt', [
            'test' => $test,
            'sessionId' => $session,
            'training' => $sessionData,
        ]);
    }

    public function submit(Request $request, Test $test, string $session): View
    {
        $sessionData = $this->resolveSession($request, $test, $session);

        $answersInput = $request->input('answers', []);

        $results = collect($sessionData['questions'])
            ->map(function (array $question, int $index) use ($answersInput) {
                $selected = $answersInput[$question['id']] ?? null;

                $selectedIds = (match (true) {
                    is_array($selected) => collect($selected),
                    is_null($selected) => collect(),
                    default => collect([$selected]),
                })
                    ->map(fn ($value) => (int) $value)
                    ->filter()
                    ->unique()
                    ->values();

                if ($question['type'] === Question::TYPE_SINGLE && $selectedIds->isNotEmpty()) {
                    $selectedIds = collect([$selectedIds->first()]);
                }

                $correctIds = collect($question['answers'])
                    ->filter(fn (array $answer) => $answer['is_correct'])
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->sort()
                    ->values();

                $selectedSorted = $selectedIds->sort()->values();

                $isCorrect = $selectedSorted->isNotEmpty()
                    && $selectedSorted->count() === $correctIds->count()
                    && $selectedSorted->every(fn ($id, $key) => $id === $correctIds->get($key));

                $selectedOptions = collect($question['answers'])
                    ->filter(fn (array $answer) => $selectedIds->contains((int) $answer['id']))
                    ->pluck('text')
                    ->values();

                $correctOptions = collect($question['answers'])
                    ->filter(fn (array $answer) => $answer['is_correct'])
                    ->pluck('text')
                    ->values();

                return [
                    'index' => $index + 1,
                    'question' => $question,
                    'selected_ids' => $selectedIds,
                    'selected_texts' => $selectedOptions,
                    'correct_texts' => $correctOptions,
                    'is_correct' => $isCorrect,
                    'points_awarded' => $isCorrect ? (int) $question['points'] : 0,
                ];
            });

        $totalQuestions = $results->count();
        $totalPoints = $results->sum(fn (array $item) => (int) $item['question']['points']);
        $earnedPoints = $results->sum('points_awarded');
        $correctCount = $results->where('is_correct', true)->count();

        $request->session()->forget('training_sessions.'.$session);

        return view('tests.training.result', [
            'test' => $test,
            'results' => $results,
            'totalQuestions' => $totalQuestions,
            'correctCount' => $correctCount,
            'totalPoints' => $totalPoints,
            'earnedPoints' => $earnedPoints,
        ]);
    }

    protected function resolveSession(Request $request, Test $test, string $session): array
    {
        $sessionData = $request->session()->get('training_sessions.'.$session);

        abort_if(
            ! $sessionData || (int) ($sessionData['test_id'] ?? null) !== $test->id,
            404
        );

        return $sessionData;
    }
}


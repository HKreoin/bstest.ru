<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Test;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'current_index' => 0,
            'results' => [],
            'last_result' => null,
        ]);

        return redirect()->route('tests.training.attempt', [
            'test' => $test,
            'session' => $sessionId,
        ]);
    }

    public function attempt(Request $request, Test $test, string $session): View
    {
        $sessionData = $this->resolveSession($request, $test, $session);

        $questions = $sessionData['questions'] ?? [];
        $totalQuestions = count($questions);

        $currentIndex = (int) ($sessionData['current_index'] ?? 0);
        abort_if($currentIndex < 0 || $currentIndex >= $totalQuestions, 404);

        $question = $questions[$currentIndex];

        $lastResult = $sessionData['last_result'] ?? null;
        $isLastQuestion = $currentIndex === $totalQuestions - 1;

        return view('tests.training.attempt', [
            'test' => $test,
            'sessionId' => $session,
            'questionIndex' => $currentIndex + 1,
            'totalQuestions' => $totalQuestions,
            'question' => $question,
            'lastResult' => $lastResult,
            'isLastQuestion' => $isLastQuestion,
        ]);
    }

    public function submit(Request $request, Test $test, string $session): View
    {
        $sessionData = $this->resolveSession($request, $test, $session);

        $questions = $sessionData['questions'] ?? [];
        $totalQuestions = count($questions);

        $currentIndex = (int) ($sessionData['current_index'] ?? 0);
        abort_if($currentIndex < 0 || $currentIndex >= $totalQuestions, 404);

        $question = $questions[$currentIndex];
        $questionId = (int) ($question['id'] ?? 0);

        $optionIds = collect($question['answers'] ?? [])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        abort_if(empty($optionIds) || $questionId <= 0, 404);

        $inRule = 'in:'.implode(',', $optionIds);

        // Валидируем только ответ на текущий вопрос.
        if ($question['type'] === Question::TYPE_SINGLE) {
            $validated = $request->validate([
                'answers.'.$questionId => ['required', 'integer', $inRule],
            ]);
        } else {
            $validated = $request->validate([
                'answers.'.$questionId => ['required', 'array', 'min:1'],
                'answers.'.$questionId.'.*' => ['required', 'integer', $inRule],
            ]);
        }

        $answersInput = $validated['answers'] ?? [];

        $selected = $answersInput[$questionId] ?? null;

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
            ->filter(fn (array $answer) => (bool) $answer['is_correct'])
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
            ->values()
            ->toArray();

        $correctOptions = collect($question['answers'])
            ->filter(fn (array $answer) => (bool) $answer['is_correct'])
            ->pluck('text')
            ->values()
            ->toArray();

        $resultItem = [
            'index' => $currentIndex + 1,
            'question' => $question,
            'selected_ids' => $selectedIds->toArray(),
            'selected_texts' => $selectedOptions,
            'correct_texts' => $correctOptions,
            'is_correct' => (bool) $isCorrect,
            'points_awarded' => $isCorrect ? (int) $question['points'] : 0,
        ];

        $results = $sessionData['results'] ?? [];
        $results[] = $resultItem;

        // Оставляем текущий вопрос, сохраняем результат и показываем его сразу же.
        $sessionData['results'] = $results;
        $sessionData['last_result'] = $resultItem;
        $request->session()->put('training_sessions.'.$session, $sessionData);

        return view('tests.training.attempt', [
            'test' => $test,
            'sessionId' => $session,
            'questionIndex' => $currentIndex + 1,
            'totalQuestions' => $totalQuestions,
            'question' => $question,
            'lastResult' => $resultItem,
            'isLastQuestion' => $currentIndex === $totalQuestions - 1,
        ]);
    }

    public function next(Request $request, Test $test, string $session): View|RedirectResponse
    {
        $sessionData = $this->resolveSession($request, $test, $session);

        $questions = $sessionData['questions'] ?? [];
        $totalQuestions = count($questions);

        $currentIndex = (int) ($sessionData['current_index'] ?? 0);
        abort_if($currentIndex < 0 || $currentIndex >= $totalQuestions, 404);

        // Переходим дальше только после ответа на текущий вопрос.
        abort_if(empty($sessionData['last_result'] ?? null), 409);

        $newIndex = $currentIndex + 1;

        // Готовим финальный результат.
        if ($newIndex >= $totalQuestions) {
            $results = $sessionData['results'] ?? [];

            $totalPoints = collect($questions)->sum(fn (array $q) => (int) ($q['points'] ?? 0));
            $earnedPoints = collect($results)->sum(fn (array $r) => (int) ($r['points_awarded'] ?? 0));
            $correctCount = collect($results)->where('is_correct', true)->count();

            $resultsForView = collect($results)->map(function (array $item) {
                return [
                    'index' => $item['index'],
                    'question' => $item['question'],
                    'selected_ids' => collect($item['selected_ids']),
                    'selected_texts' => collect($item['selected_texts']),
                    'correct_texts' => collect($item['correct_texts']),
                    'is_correct' => $item['is_correct'],
                    'points_awarded' => $item['points_awarded'],
                ];
            });

            $request->session()->forget('training_sessions.'.$session);

            return view('tests.training.result', [
                'test' => $test,
                'results' => $resultsForView,
                'totalQuestions' => $totalQuestions,
                'correctCount' => $correctCount,
                'totalPoints' => (int) $totalPoints,
                'earnedPoints' => (int) $earnedPoints,
            ]);
        }

        // Иначе — показываем следующий вопрос.
        $sessionData['current_index'] = $newIndex;
        unset($sessionData['last_result']);
        $request->session()->put('training_sessions.'.$session, $sessionData);

        return redirect()->route('tests.training.attempt', [
            'test' => $test,
            'session' => $session,
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

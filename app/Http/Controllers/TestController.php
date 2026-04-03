<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index(Request $request): View
    {
        $tests = Test::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->paginate(9);

        $recentAttempts = collect($request->session()->get('test_attempts', []))
            ->sortByDesc('started_at')
            ->take(5)
            ->values();

        return view('tests.index', [
            'tests' => $tests,
            'lastAttempts' => $recentAttempts,
        ]);
    }

    public function show(Request $request, Test $test): View
    {
        abort_unless($test->is_active, 404);

        $sessionAttempts = collect($request->session()->get('test_attempts', []))
            ->filter(fn (array $attempt) => $attempt['test_id'] === $test->id)
            ->sortByDesc('started_at')
            ->values();

        return view('tests.show', [
            'test' => $test,
            'recentAttempts' => $sessionAttempts->take(10),
        ]);
    }

    public function start(Request $request, Test $test): RedirectResponse
    {
        abort_unless($test->is_active, 404);

        $request->merge([
            'participant_name' => preg_replace('/\s+/u', ' ', trim((string) $request->input('participant_name', ''))),
        ]);

        $data = $request->validate(
            [
                'participant_name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[\p{Cyrillic}\s\-\.]+$/u',
                    fn (string $attribute, mixed $value, \Closure $fail) => $this->validateParticipantFio((string) $value, $fail),
                ],
                'pd_consent' => ['accepted'],
            ],
            [
                'participant_name.regex' => 'Только кириллица, пробел, дефис и точка (формат Фамилия Имя Отчество или Фамилия И. О.).',
            ],
        );

        return DB::transaction(function () use ($request, $test, $data): RedirectResponse {
            $questions = $test->drawQuestionsForAttempt();

            abort_if($questions->isEmpty(), 400, 'Нет вопросов для теста.');

            $attempt = TestAttempt::create([
                'test_id' => $test->id,
                'participant_name' => $data['participant_name'],
                'participant_email' => null,
                'personal_data_consent_at' => now(),
                'total_questions' => $questions->count(),
                'started_at' => now(),
            ]);

            foreach ($questions as $question) {
                $attempt->questionAttempts()->create([
                    'question_id' => $question->id,
                    'selected_option_ids' => [],
                    'points_awarded' => 0,
                ]);
            }

            $attemptSession = [
                'id' => $attempt->id,
                'test_id' => $attempt->test_id,
                'test_slug' => $test->slug,
                'test_title' => $test->title,
                'participant_name' => $attempt->participant_name,
                'score_percent' => null,
                'passed' => false,
                'started_at' => $attempt->started_at?->timestamp,
                'completed_at' => null,
            ];

            $sessionAttempts = collect($request->session()->get('test_attempts', []))
                ->filter(fn (array $item) => $item['id'] !== $attempt->id)
                ->prepend($attemptSession)
                ->take(20)
                ->values()
                ->all();

            $request->session()->put('test_attempts', $sessionAttempts);
            $this->storeAllowedAttemptId($request, $attempt->id);

            return redirect()->route('attempts.show', $attempt);
        });
    }

    /**
     * Допустимо: «Фамилия Имя Отчество» (3 слова) или «Фамилия И. О.».
     */
    protected function validateParticipantFio(string $value, \Closure $fail): void
    {
        $parts = preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
        $word = '/^[\p{Cyrillic}]{2,}(?:-[\p{Cyrillic}]+)*$/u';
        $initial = '/^[\p{Cyrillic}]\.$/u';
        $initialsPair = '/^[\p{Cyrillic}]\.[\p{Cyrillic}]\.$/u';

        $msg = 'Укажите ФИО кириллицей: «Фамилия Имя Отчество» или «Фамилия И. О.»';

        if (count($parts) === 2) {
            if (preg_match($word, $parts[0]) && preg_match($initialsPair, $parts[1])) {
                return;
            }
            $fail($msg);

            return;
        }

        if (count($parts) === 3) {
            if (! preg_match($word, $parts[0])) {
                $fail($msg);

                return;
            }
            $fullFull = preg_match($word, $parts[1]) && preg_match($word, $parts[2]);
            $initials = preg_match($initial, $parts[1]) && preg_match($initial, $parts[2]);
            if ($fullFull || $initials) {
                return;
            }
        }

        $fail($msg);
    }

    protected function storeAllowedAttemptId(Request $request, int $attemptId): void
    {
        $allowed = collect($request->session()->get('allowed_attempt_ids', []))
            ->filter(fn (int $id) => $id !== $attemptId)
            ->push($attemptId)
            ->take(50)
            ->values()
            ->all();

        $request->session()->put('allowed_attempt_ids', $allowed);
    }
}

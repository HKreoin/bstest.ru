<?php

namespace App\Http\Controllers;

use App\Models\Test;
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

        return view('tests.index', [
            'tests' => $tests,
            'lastAttempts' => $request->user()
                ->testAttempts()
                ->with('test')
                ->latest('completed_at')
                ->limit(5)
                ->get(),
        ]);
    }

    public function show(Request $request, Test $test): View
    {
        abort_unless($test->is_active, 404);

        $attemptsQuery = $request->user()
            ->testAttempts()
            ->where('test_id', $test->id)
            ->latest('created_at');

        $latestAttempt = (clone $attemptsQuery)->first();

        $recentAttempts = (clone $attemptsQuery)->limit(10)->get();

        return view('tests.show', [
            'test' => $test,
            'latestAttempt' => $latestAttempt,
            'recentAttempts' => $recentAttempts,
        ]);
    }

    public function start(Request $request, Test $test): RedirectResponse
    {
        abort_unless($test->is_active, 404);

        return DB::transaction(function () use ($request, $test): RedirectResponse {
            $questions = $test->drawQuestionsForAttempt();

            abort_if($questions->isEmpty(), 400, 'Нет вопросов для теста.');

            $attempt = $request->user()->testAttempts()->create([
                'test_id' => $test->id,
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

            return redirect()->route('attempts.show', $attempt);
        });
    }
}



<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Прохождение: {{ $attempt->test->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>Всего вопросов: <span class="font-semibold text-gray-900">{{ $attempt->questionAttempts->count() }}</span></p>
                        @if ($attempt->test->time_limit_minutes)
                            <p>Время на прохождение: <span class="font-semibold text-gray-900">{{ $attempt->test->time_limit_minutes }} минут</span></p>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('tests.show', $attempt->test) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Вернуться к описанию теста
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('attempts.submit', $attempt) }}" class="space-y-6">
                    @csrf

                    @foreach ($attempt->questionAttempts as $index => $questionAttempt)
                        @php
                            $question = $questionAttempt->question;
                            $selectedOptions = $questionAttempt->selected_option_ids ?? [];
                            $oldInput = old("answers.{$questionAttempt->id}");

                            if ($question->isMultipleChoice()) {
                                $value = is_array($oldInput) ? $oldInput : $selectedOptions;
                            } else {
                                $value = is_array($oldInput) ? ($oldInput[0] ?? null) : ($oldInput ?? ($selectedOptions[0] ?? null));
                            }
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-5 space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="text-base font-semibold text-gray-900">
                                    {{ $index + 1 }}. {{ $question->text }}
                                </h3>
                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                                    {{ $question->points }} балл{{ $question->points === 1 ? '' : 'ов' }}
                                </span>
                            </div>

                            @if ($question->isSingleChoice())
                                <fieldset class="space-y-3">
                                    @foreach ($question->answerOptions as $option)
                                        <label class="flex items-start gap-3">
                                            <input
                                                type="radio"
                                                name="answers[{{ $questionAttempt->id }}]"
                                                value="{{ $option->id }}"
                                                @checked((int) $value === $option->id)
                                                class="mt-1 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >
                                            <span class="text-sm text-gray-700">{{ $option->text }}</span>
                                        </label>
                                    @endforeach
                                </fieldset>
                            @else
                                <fieldset class="space-y-3">
                                    @foreach ($question->answerOptions as $option)
                                        <label class="flex items-start gap-3">
                                            <input
                                                type="checkbox"
                                                name="answers[{{ $questionAttempt->id }}][]"
                                                value="{{ $option->id }}"
                                                @checked(collect($value)->contains($option->id))
                                                class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >
                                            <span class="text-sm text-gray-700">{{ $option->text }}</span>
                                        </label>
                                    @endforeach
                                </fieldset>
                            @endif
                        </div>
                    @endforeach

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Завершить попытку
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


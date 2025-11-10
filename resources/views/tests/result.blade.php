<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Результат: {{ $attempt->test->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (request()->boolean('print'))
                <script>
                    window.addEventListener('load', () => window.print());
                </script>
            @endif
            @php
                $completedAtLocal = $attempt->completed_at?->setTimezone('Europe/Moscow');
                $timeSpentSeconds = $attempt->calculatedTimeSpentSeconds();
            @endphp
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1 text-sm text-gray-600">
                        <p>Дата завершения (GMT+3): <span class="font-medium text-gray-900">{{ $completedAtLocal?->format('d.m.Y H:i') ?? '—' }}</span></p>
                        <p>Время прохождения: <span class="font-medium text-gray-900">{{ $timeSpentSeconds !== null ? gmdate('H:i:s', $timeSpentSeconds) : '—' }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $attempt->score_percent !== null ? $attempt->score_percent . '%' : '—' }}
                        </p>
                        <p class="text-sm text-gray-500">Проходной процент: {{ $attempt->test->passing_score_percent }}%</p>
                        @if ($attempt->passed)
                            <span class="mt-2 inline-flex items-center rounded-full bg-green-100 px-4 py-1 text-sm font-medium text-green-800">
                                Тест пройден
                            </span>
                        @else
                            <span class="mt-2 inline-flex items-center rounded-full bg-red-100 px-4 py-1 text-sm font-medium text-red-800">
                                Не пройден
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    Правильных ответов: <span class="font-medium text-gray-900">{{ $attempt->correct_questions }}</span> из {{ $attempt->total_questions }}
                </div>
                <div class="text-sm text-gray-600">
                    Участник: <span class="font-medium text-gray-900">{{ $attempt->participant_name }}</span>
                    @if ($attempt->participant_email)
                        (<span class="text-gray-500">{{ $attempt->participant_email }}</span>)
                    @endif
                </div>
                <div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('attempts.protocol', $attempt) }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700">
                            Скачать протокол (Word)
                        </a>
                        <a href="{{ route('attempts.show', [$attempt, 'print' => 1]) }}" target="_blank" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                            Распечатать протокол
                        </a>
                        <a href="{{ route('tests.show', $attempt->test) }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Вернуться к тесту
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Разбор вопросов</h3>
                <div class="space-y-6">
                    @foreach ($attempt->questionAttempts as $index => $questionAttempt)
                        @php
                            $question = $questionAttempt->question;
                            $selectedIds = collect($questionAttempt->selected_option_ids ?? []);
                            $correctIds = $question->correctOptionIds();
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-5 space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="text-base font-semibold text-gray-900">
                                        {{ $index + 1 }}. {{ $question->text }}
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Баллы: {{ $question->points }}, тип: {{ $question->isSingleChoice() ? 'один вариант' : 'несколько вариантов' }}
                                    </p>
                                </div>
                                @if ($questionAttempt->is_correct)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                        Верно
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                                        Неверно
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700">Ваши выборы</p>
                                <ul class="space-y-2">
                                    @foreach ($question->answerOptions as $option)
                                        @php
                                            $isSelected = $selectedIds->contains($option->id);
                                            $isCorrect = $correctIds->contains($option->id);
                                        @endphp
                                        <li class="flex items-start gap-3 text-sm">
                                            <span class="mt-1 h-2 w-2 rounded-full {{ $isCorrect ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                            <div>
                                                <p class="{{ $isCorrect ? 'text-green-700' : ($isSelected ? 'text-red-700' : 'text-gray-700') }}">
                                                    {{ $option->text }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    @if ($isCorrect && $isSelected)
                                                        выбрано, верный вариант
                                                    @elseif ($isCorrect)
                                                        правильный вариант (не выбран)
                                                    @elseif ($isSelected)
                                                        выбрано
                                                    @else
                                                        не выбрано
                                                    @endif
                                                </p>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


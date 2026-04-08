<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Тренажёр: {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                <div class="text-sm text-gray-600">
                    <p>
                        Вопрос №{{ $questionIndex }} ({{ $progressIndex }} из {{ $totalQuestions }}).
                        Ограничений по времени нет, результаты не сохраняются.
                    </p>
                </div>

                @if (empty($lastResult))
                    <form method="POST" action="{{ route('tests.training.submit', ['test' => $test, 'session' => $sessionId]) }}" class="space-y-6">
                        @csrf

                        <div class="border border-gray-200 rounded-lg p-5 space-y-4">
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ $questionIndex }}. {{ $question['text'] }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Баллы: {{ $question['points'] }},
                                тип: {{ $question['type'] === \App\Models\Question::TYPE_SINGLE ? 'один вариант' : 'несколько вариантов' }}
                            </p>

                            <div class="space-y-3">
                                @php
                                    $inputName = $question['type'] === \App\Models\Question::TYPE_SINGLE
                                        ? "answers[{$question['id']}]"
                                        : "answers[{$question['id']}][]";
                                    $oldValue = old("answers.{$question['id']}");
                                @endphp

                                @foreach ($question['answers'] as $option)
                                    @php
                                        $isChecked = $question['type'] === \App\Models\Question::TYPE_SINGLE
                                            ? (string) ($oldValue ?? '') === (string) $option['id']
                                            : collect($oldValue)->contains((string) $option['id']);
                                    @endphp
                                    <label class="flex items-start gap-3 text-sm text-gray-700">
                                        <input
                                            type="{{ $question['type'] === \App\Models\Question::TYPE_SINGLE ? 'radio' : 'checkbox' }}"
                                            name="{{ $inputName }}"
                                            value="{{ $option['id'] }}"
                                            class="mt-1 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                            {{ $isChecked ? 'checked' : '' }}
                                        >
                                        <span>{{ $option['text'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('tests.training.configure', $test) }}" class="text-sm text-gray-500 hover:text-gray-700">
                                ← Настроить заново
                            </a>

                            <button type="submit" class="ml-auto inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                                Ответить
                            </button>
                        </div>
                    </form>
                @else
                    <div class="border border-gray-200 rounded-lg p-5 space-y-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    {{ $questionIndex }}. {{ $question['text'] }}
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Баллы: {{ $question['points'] }},
                                    тип: {{ $question['type'] === \App\Models\Question::TYPE_SINGLE ? 'один вариант' : 'несколько вариантов' }}
                                </p>
                            </div>

                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                {{ $lastResult['is_correct'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $lastResult['is_correct'] ? 'Ответ верный' : 'Ответ неверный' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700">Выбранные варианты</p>
                                @if (empty($lastResult['selected_texts']))
                                    <p class="text-sm text-gray-500">Ответ не выбран</p>
                                @else
                                    <ul class="list-disc list-inside text-sm text-gray-700">
                                        @foreach ($lastResult['selected_texts'] as $text)
                                            <li>{{ $text }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700">Правильные ответы</p>
                                @if (empty($lastResult['correct_texts']))
                                    <p class="text-sm text-gray-500">—</p>
                                @else
                                    <ul class="list-disc list-inside text-sm text-green-700">
                                        @foreach ($lastResult['correct_texts'] as $text)
                                            <li>{{ $text }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <a href="{{ route('tests.training.configure', $test) }}" class="text-sm text-gray-500 hover:text-gray-700">
                                ← Настроить заново
                            </a>

                            <form method="POST" action="{{ route('tests.training.next', ['test' => $test, 'session' => $sessionId]) }}" class="ml-auto">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                                    {{ $isLastQuestion ? 'Завершить тренировку' : 'Дальше' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>



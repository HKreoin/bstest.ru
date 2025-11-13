<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Тренажёр: {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1 text-sm text-gray-600">
                        <p>Всего вопросов: <span class="font-medium text-gray-900">{{ $totalQuestions }}</span></p>
                        <p>Правильных ответов: <span class="font-medium text-gray-900">{{ $correctCount }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $totalPoints > 0 ? number_format(($earnedPoints / $totalPoints) * 100, 2) : '0' }}%
                        </p>
                        <p class="text-sm text-gray-500">Баллы: {{ $earnedPoints }} / {{ $totalPoints }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('tests.training.configure', $test) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        Настроить заново
                    </a>
                    <a href="{{ route('tests.show', $test) }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                        Вернуться к описанию теста
                    </a>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Разбор вопросов</h3>
                <div class="space-y-6">
                    @foreach ($results as $item)
                        <div class="border border-gray-200 rounded-lg p-5 space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h4 class="text-base font-semibold text-gray-900">
                                        {{ $item['index'] }}. {{ $item['question']['text'] }}
                                    </h4>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Баллы: {{ $item['question']['points'] }},
                                        тип: {{ $item['question']['type'] === \App\Models\Question::TYPE_SINGLE ? 'один вариант' : 'несколько вариантов' }}
                                    </p>
                                </div>
                                @if ($item['is_correct'])
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
                                <p class="text-sm font-medium text-gray-700">Выбранные варианты</p>
                                @if ($item['selected_texts']->isEmpty())
                                    <p class="text-sm text-gray-500">Ответ не выбран</p>
                                @else
                                    <ul class="list-disc list-inside text-sm text-gray-700">
                                        @foreach ($item['selected_texts'] as $text)
                                            <li>{{ $text }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-700">Правильные ответы</p>
                                <ul class="list-disc list-inside text-sm text-green-700">
                                    @foreach ($item['correct_texts'] as $text)
                                        <li>{{ $text }}</li>
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



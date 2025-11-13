<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Тренажёр: {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                <div class="text-sm text-gray-600">
                    <p>Вопросов в тренировке: {{ count($training['questions']) }}. Ограничений по времени нет, результаты не сохраняются.</p>
                </div>

                <form method="POST" action="{{ route('tests.training.submit', ['test' => $test, 'session' => $sessionId]) }}" class="space-y-8">
                    @csrf

                    @foreach ($training['questions'] as $index => $question)
                        <div class="border border-gray-200 rounded-lg p-5 space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">
                                        {{ $index + 1 }}. {{ $question['text'] }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Баллы: {{ $question['points'] }},
                                        тип: {{ $question['type'] === \App\Models\Question::TYPE_SINGLE ? 'один вариант' : 'несколько вариантов' }}
                                    </p>
                                </div>
                            </div>

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
                    @endforeach

                    <div class="flex items-center gap-3">
                        <a href="{{ route('tests.training.configure', $test) }}" class="text-sm text-gray-500 hover:text-gray-700">
                            ← Настроить заново
                        </a>
                        <button type="submit" class="ml-auto inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Проверить ответы
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>



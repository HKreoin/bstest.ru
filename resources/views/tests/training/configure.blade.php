<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Тренажёр: {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                <p class="text-sm text-gray-600">
                    Выберите параметры тренировки. Результаты не сохраняются, ограничений по времени нет.
                </p>

                <form
                    method="POST"
                    action="{{ route('tests.training.start', $test) }}"
                    class="space-y-6"
                    x-data="{
                        order: '{{ old('order', 'original') }}',
                        totalQuestions: {{ (int) $test->questions_count }},
                        questionCount: {{ (int) old('question_count', $test->questions_count) }},
                        startFrom: {{ (int) old('start_from', 1) }},
                        availableTail() {
                            return Math.max(1, this.totalQuestions - this.startFrom + 1);
                        },
                        normalizeStartFrom() {
                            if (!Number.isFinite(this.startFrom)) this.startFrom = 1;
                            this.startFrom = Math.min(Math.max(1, this.startFrom), this.totalQuestions);
                        },
                        normalizeQuestionCount() {
                            if (!Number.isFinite(this.questionCount)) this.questionCount = 1;
                            const max = this.order === 'original' ? this.availableTail() : this.totalQuestions;
                            this.questionCount = Math.min(Math.max(1, this.questionCount), max);
                        },
                    }"
                >
                    @csrf

                    <div>
                        <label for="question_count" class="block text-sm font-medium text-gray-700">Количество вопросов</label>
                        <input
                            type="number"
                            id="question_count"
                            name="question_count"
                            min="1"
                            :max="order === 'original' ? availableTail() : totalQuestions"
                            x-model.number="questionCount"
                            @input="normalizeQuestionCount()"
                            required
                            class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-700"
                        >
                        <p class="mt-1 text-xs text-gray-500">Всего доступно: {{ $test->questions_count }}</p>
                        @error('question_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <fieldset>
                        <legend class="text-sm font-medium text-gray-700 mb-2">Порядок вопросов</legend>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input
                                    type="radio"
                                    name="order"
                                    value="original"
                                    {{ old('order', 'original') === 'original' ? 'checked' : '' }}
                                    @change="order = 'original'; normalizeStartFrom(); normalizeQuestionCount();"
                                    class="text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                >
                                По порядку
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input
                                    type="radio"
                                    name="order"
                                    value="random"
                                    {{ old('order') === 'random' ? 'checked' : '' }}
                                    @change="order = 'random'; normalizeQuestionCount();"
                                    class="text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                >
                                В случайном порядке
                            </label>
                        </div>
                        @error('order')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div x-show="order === 'original'" x-cloak>
                        <label for="start_from" class="block text-sm font-medium text-gray-700">Начать с вопроса №</label>
                        <input
                            type="number"
                            id="start_from"
                            name="start_from"
                            min="1"
                            :max="totalQuestions"
                            x-model.number="startFrom"
                            @input="normalizeStartFrom(); normalizeQuestionCount();"
                            class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-700"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            Доступно от 1 до {{ $test->questions_count }}. При изменении старта количество вопросов автоматически ограничивается доступным хвостом.
                        </p>
                        @error('start_from')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('tests.show', $test) }}" class="text-sm text-gray-500 hover:text-gray-700">
                            ← Вернуться к тесту
                        </a>
                        <button type="submit" class="ml-auto inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                            Начать тренировку
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>



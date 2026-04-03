<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Режим тренажёра</h3>
                <p class="text-sm text-gray-600">
                    Пройдите тренировку без ограничений по времени и без сохранения результатов. Можно выбрать количество вопросов и порядок прохождения.
                </p>
                <a
                    href="{{ route('tests.training.configure', $test) }}"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                >
                    Настроить тренировку
                </a>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6 space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Сдать экзамен</h3>

                @if ($test->description)
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $test->description }}</p>
                @endif

                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">Вопросов за попытку</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $test->questions_per_attempt ?? $test->questions()->count() }}
                        </dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">Проходной процент</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $test->passing_score_percent }}%
                        </dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">Лимит времени</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            @if ($test->time_limit_minutes)
                                {{ $test->time_limit_minutes }} мин
                            @else
                                Без ограничения
                            @endif
                        </dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('tests.start', $test) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="participant_name" class="block text-sm font-medium text-gray-700">ФИО</label>
                        <input
                            type="text"
                            id="participant_name"
                            name="participant_name"
                            value="{{ old('participant_name', auth()->user()?->name) }}"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-700"
                        >
                        @error('participant_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="participant_email" class="block text-sm font-medium text-gray-700">Email (необязательно)</label>
                        <input
                            type="email"
                            id="participant_email"
                            name="participant_email"
                            value="{{ old('participant_email', auth()->user()?->email) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-700"
                        >
                        @error('participant_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-start gap-2">
                        <input
                            type="checkbox"
                            id="pd_consent"
                            name="pd_consent"
                            value="1"
                            class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            {{ old('pd_consent') ? 'checked' : '' }}
                            required
                        >
                        <label for="pd_consent" class="text-sm text-gray-700">
                            Согласен(на) на обработку персональных данных (ФИО и при необходимости email) в целях прохождения тестирования,
                            в соответствии с
                            <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 hover:text-indigo-800">политикой обработки персональных данных</a>.
                        </label>
                    </div>
                    @error('pd_consent')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-600">
                        После нажатия «Начать тест» будет сформирована новая попытка. Вы сможете вернуться к результату из этой страницы или сразу после завершения теста.
                    </p>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                        Начать тест
                    </button>
                </form>
            </div>

            @php
                $recentAttemptsList = collect($recentAttempts);
            @endphp
            @if ($recentAttemptsList->isNotEmpty())
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ваши последние попытки</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Результат</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действие</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($recentAttemptsList as $attempt)
                                    @php
                                        $isCompleted = !empty($attempt['completed_at']);
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            @php
                                                $timestamp = $attempt['completed_at'] ?? $attempt['started_at'] ?? null;
                                            @endphp
                                            {{ $timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp, 'Europe/Moscow')->format('d.m.Y H:i') : '—' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if ($isCompleted)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                                    Завершено
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    В процессе
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            @if ($isCompleted)
                                                {{ $attempt['score_percent'] !== null ? $attempt['score_percent'] : '—' }}%
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm text-indigo-600">
                                            <a href="{{ route('attempts.show', ['testAttempt' => $attempt['id']]) }}" class="hover:text-indigo-900">
                                                Открыть
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>


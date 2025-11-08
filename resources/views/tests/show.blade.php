<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $test->title }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white shadow sm:rounded-lg p-6 space-y-4">
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
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">Последняя попытка</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if ($latestAttempt)
                                {{ $latestAttempt->created_at->format('d.m.Y H:i') }}
                                @if ($latestAttempt->isCompleted())
                                    — результат {{ $latestAttempt->score_percent ?? '—' }}%
                                @else
                                    — в процессе
                                @endif
                            @else
                                ещё не проходили
                            @endif
                        </dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('tests.start', $test) }}" class="sm:flex sm:items-center sm:justify-between">
                    @csrf
                    <p class="text-sm text-gray-600">
                        Нажмите «Начать тест», чтобы сформировать новую попытку. Предыдущие попытки останутся в истории.
                    </p>
                    <button type="submit" class="mt-4 inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 sm:mt-0">
                        Начать тест
                    </button>
                </form>
            </div>

            @if ($recentAttempts->isNotEmpty())
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">История попыток</h3>
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
                                @foreach ($recentAttempts as $attempt)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ $attempt->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if ($attempt->isCompleted())
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
                                            @if ($attempt->isCompleted())
                                                {{ $attempt->score_percent ?? '—' }}%
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm text-indigo-600">
                                            <a href="{{ route('attempts.show', $attempt) }}" class="hover:text-indigo-900">
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


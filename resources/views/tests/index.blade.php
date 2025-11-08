<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Доступные тесты
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Последние попытки
                </h3>
                @if ($lastAttempts->isEmpty())
                    <p class="text-sm text-gray-500">Вы ещё не проходили тесты.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тест</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Результат</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($lastAttempts as $attempt)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            <a href="{{ route('tests.show', $attempt->test) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $attempt->test->title }}
                                            </a>
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
                                        <td class="px-4 py-2 text-sm text-gray-500">
                                            {{ optional($attempt->completed_at ?? $attempt->created_at)->format('d.m.Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($tests as $test)
                    <div class="bg-white shadow sm:rounded-lg p-6 flex flex-col">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $test->title }}</h3>
                        @if ($test->description)
                            <p class="mt-2 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($test->description, 160) }}</p>
                        @endif
                        <dl class="mt-4 space-y-1 text-sm text-gray-500">
                            @if ($test->questions_per_attempt)
                                <div>Вопросов в попытке: <span class="font-medium text-gray-700">{{ $test->questions_per_attempt }}</span></div>
                            @endif
                            @if ($test->time_limit_minutes)
                                <div>Время: <span class="font-medium text-gray-700">{{ $test->time_limit_minutes }} мин</span></div>
                            @endif
                            <div>Проходной балл: <span class="font-medium text-gray-700">{{ $test->passing_score_percent }}%</span></div>
                        </dl>
                        <div class="mt-6">
                            <a href="{{ route('tests.show', $test) }}" class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                                Открыть
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Нет доступных тестов.</p>
                @endforelse
            </div>

            {{ $tests->links() }}
        </div>
    </div>
</x-app-layout>


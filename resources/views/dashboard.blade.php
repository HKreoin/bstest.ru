<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Личный кабинет
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-2">
                    <p class="text-lg font-semibold text-gray-900">
                        Привет, {{ auth()->user()->name }}!
                    </p>
                    <p class="text-sm text-gray-600">
                        Отсюда можно перейти к прохождению тестов или открыть административную панель (если есть права администратора).
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <a
                    href="{{ route('tests.index') }}"
                    class="block bg-white shadow-sm sm:rounded-lg border border-transparent hover:border-indigo-200 transition"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-indigo-700">Доступные тесты</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Перейти к списку тестов и начать прохождение.
                        </p>
                    </div>
                </a>

                @if (auth()->user()->is_admin)
                    <a
                        href="{{ url('/admin') }}"
                        class="block bg-white shadow-sm sm:rounded-lg border border-transparent hover:border-amber-200 transition"
                    >
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-amber-700">Админ-панель</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Управление тестами, вопросами и пользователями через Filament.
                            </p>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

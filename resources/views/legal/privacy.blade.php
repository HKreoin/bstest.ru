<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Политика в отношении обработки персональных данных
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl space-y-6 px-4 text-sm text-gray-700 sm:px-6 lg:px-8">
            @include('legal.partials.privacy-body', ['useModalTriggers' => false])
        </div>
    </div>
</x-app-layout>

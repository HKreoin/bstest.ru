<footer class="border-t border-gray-200 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-xs text-gray-600">
            <button type="button" class="cursor-pointer border-0 bg-transparent text-gray-600 hover:text-gray-900" @click="$dispatch('open-legal-privacy')">
                Персональные данные
            </button>
            <span class="select-none text-gray-300" aria-hidden="true">·</span>
            <button type="button" class="cursor-pointer border-0 bg-transparent text-gray-600 hover:text-gray-900" @click="$dispatch('open-legal-cookies')">
                Файлы cookie
            </button>
        </div>
    </div>
</footer>

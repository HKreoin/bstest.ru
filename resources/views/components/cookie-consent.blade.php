<div
    x-data="{
        key: 'bstest_cookie_consent_v1',
        show: false,
        init() {
            try {
                this.show = localStorage.getItem(this.key) !== '1';
            } catch (e) {
                this.show = true;
            }
        },
        accept() {
            try {
                localStorage.setItem(this.key, '1');
            } catch (e) {}
            this.show = false;
        },
    }"
    x-show="show"
    x-cloak
    class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white/95 px-4 py-4 shadow-lg backdrop-blur-sm sm:px-6"
    style="display: none;"
>
    <div class="mx-auto flex max-w-5xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-700">
            Сайт использует файлы cookie (в том числе для сессии и защиты форм) и может обрабатывать персональные данные.
            Подробнее — в&nbsp;<a href="{{ route('legal.privacy') }}" class="font-medium text-indigo-600 hover:text-indigo-800">политике обработки персональных данных</a>
            и&nbsp;<a href="{{ route('legal.cookies') }}" class="font-medium text-indigo-600 hover:text-indigo-800">политике cookie</a>.
        </p>
        <button
            type="button"
            @click="accept()"
            class="shrink-0 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
        >
            Понятно
        </button>
    </div>
</div>

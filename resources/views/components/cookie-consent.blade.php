<div
    x-data="{
        acceptUrl: @js(route('cookie-consent.store')),
        csrfToken: @js(csrf_token()),
        show: false,
        hasConsentCookie() {
            return document.cookie.split(';').some((row) => {
                const t = row.trim();
                const i = t.indexOf('=');
                if (i === -1) {
                    return false;
                }
                const name = t.slice(0, i);
                const val = t.slice(i + 1);
                return name === 'bstest_cookie_consent' && val === '1';
            });
        },
        init() {
            const fromServer = document.documentElement.getAttribute('data-cookie-consent') === '1';
            const fromCookie = this.hasConsentCookie();
            this.show = !(fromServer || fromCookie);
        },
        async accept() {
            try {
                await fetch(this.acceptUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        Accept: 'application/json',
                    },
                });
                document.documentElement.setAttribute('data-cookie-consent', '1');
            } catch (e) {}
            this.show = false;
        },
    }"
    x-show="show"
    x-cloak
    class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white/95 px-4 py-4 shadow-lg backdrop-blur-sm sm:px-6"
>
    <div class="mx-auto flex max-w-5xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-700">
            Сайт использует файлы cookie (в том числе для сессии и защиты форм) и может обрабатывать персональные данные.
            Подробнее — в&nbsp;<button type="button" class="font-medium text-indigo-600 hover:text-indigo-800" @click="$dispatch('open-legal-privacy')">политике обработки персональных данных</button>
            и&nbsp;<button type="button" class="font-medium text-indigo-600 hover:text-indigo-800" @click="$dispatch('open-legal-cookies')">политике cookie</button>.
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

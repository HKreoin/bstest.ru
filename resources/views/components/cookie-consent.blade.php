@unless (request()->cookie('bstest_cookie_consent') === '1')
    <div
        id="cookie-consent-bar"
        class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white/95 px-4 py-4 shadow-lg backdrop-blur-sm sm:px-6"
    >
        <div class="mx-auto flex max-w-5xl flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-700">
                Сайт использует файлы cookie (в том числе для сессии и защиты форм) и может обрабатывать персональные данные.
                Подробнее — в&nbsp;<button type="button" class="font-medium text-indigo-600 hover:text-indigo-800" data-cookie-open-privacy>политике обработки персональных данных</button>
                и&nbsp;<button type="button" class="font-medium text-indigo-600 hover:text-indigo-800" data-cookie-open-cookies>политике cookie</button>.
            </p>
            <button
                type="button"
                id="cookie-consent-accept"
                class="shrink-0 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
            >
                Понятно
            </button>
        </div>
    </div>

    <script>
        (function () {
            var bar = document.getElementById('cookie-consent-bar');
            var btn = document.getElementById('cookie-consent-accept');
            if (!bar || !btn) {
                return;
            }

            var url = @json(route('cookie-consent.store'));
            var tokenMeta = document.querySelector('meta[name="csrf-token"]');

            function dispatchLegal(name) {
                window.dispatchEvent(new CustomEvent(name, { bubbles: true }));
            }

            bar.querySelectorAll('[data-cookie-open-privacy]').forEach(function (el) {
                el.addEventListener('click', function () {
                    dispatchLegal('open-legal-privacy');
                });
            });
            bar.querySelectorAll('[data-cookie-open-cookies]').forEach(function (el) {
                el.addEventListener('click', function () {
                    dispatchLegal('open-legal-cookies');
                });
            });

            btn.addEventListener('click', function () {
                var headers = {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                };
                if (tokenMeta && tokenMeta.content) {
                    headers['X-CSRF-TOKEN'] = tokenMeta.content;
                }
                fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: headers,
                })
                    .then(function () {
                        bar.remove();
                        document.documentElement.setAttribute('data-cookie-consent', '1');
                    })
                    .catch(function () {
                        bar.remove();
                    });
            });
        })();
    </script>
@endunless

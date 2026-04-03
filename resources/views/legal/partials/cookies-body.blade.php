@php
    $useModalTriggers = $useModalTriggers ?? false;
@endphp

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">Что такое cookie</h3>
    <p>
        Файлы cookie — небольшие фрагменты данных, которые браузер сохраняет на устройстве пользователя.
        Они помогают Сайту «{{ config('app.name') }}» работать стабильно и безопасно.
    </p>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">Какие cookie мы используем</h3>
    <ul class="list-disc space-y-2 pl-5">
        <li>
            <strong>Сессионные cookie</strong> — поддерживают сеанс пользователя на Сайте (например, после входа или при прохождении теста).
            Обычно удаляются после закрытия браузера или по истечении времени жизни сессии на сервере.
        </li>
        <li>
            <strong>Cookie защиты форм (CSRF)</strong> — используются фреймворком Laravel для предотвращения подделки запросов при отправке форм.
        </li>
    </ul>
    <p class="text-xs text-gray-500">
        На публичной части Сайта не используются рекламные или аналитические cookie третьих сторон, если вы отдельно не подключили такие сервисы на своей инфраструктуре.
    </p>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">Согласие и отключение</h3>
    <p>
        Продолжая пользоваться Сайтом после отображения уведомления о cookie, вы подтверждаете, что ознакомлены с их использованием.
        Вы можете отключить cookie в настройках браузера; в этом случае часть функций Сайта (сессии, отправка форм) может работать некорректно.
    </p>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">Персональные данные</h3>
    <p>
        Сведения об обработке персональных данных — в&nbsp;
        @if ($useModalTriggers)
            <button
                type="button"
                class="font-medium text-indigo-600 hover:text-indigo-800"
                @click="legalPrivacy = true; legalCookies = false"
            >политике обработки персональных данных</button>.
        @else
            <a href="{{ route('legal.privacy') }}" class="font-medium text-indigo-600 hover:text-indigo-800">политике обработки персональных данных</a>.
        @endif
    </p>
</section>

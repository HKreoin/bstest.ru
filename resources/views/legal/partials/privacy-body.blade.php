@php
    $useModalTriggers = $useModalTriggers ?? false;
@endphp

<p class="text-xs text-gray-500">
    Политика составлена с учётом требований Федерального закона № 152-ФЗ. При смене реквизитов или целей обработки обновите текст по рекомендации юриста.
</p>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">1. Общие положения</h3>
    <p>
        Настоящая политика определяет порядок обработки и защиты персональных данных пользователей сервиса
        «{{ config('app.name') }}» (далее — Сайт) в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ
        «О персональных данных».
    </p>
</section>

<section class="space-y-3">
    <h3 class="text-base font-semibold text-gray-900">2. Оператор персональных данных</h3>
    <p>
        Оператором персональных данных является
        <strong>общество с ограниченной ответственностью «ЦИТ «БиСофт»»</strong>
        (ООО «ЦИТ «БиСофт»», далее — Оператор). Сайт в сети Интернет по адресу
        <strong>bstest.ru</strong> (далее — Сайт) администрируется Оператором.
    </p>
    <ul class="list-none space-y-1 pl-0 text-gray-700">
        <li><span class="text-gray-500">ИНН:</span> 1644050612</li>
        <li><span class="text-gray-500">ОГРН:</span> 1081644003385</li>
        <li>
            <span class="text-gray-500">Юридический адрес:</span>
            423452, Республика Татарстан, Альметьевский район, г. Альметьевск, ул. Ленина, д. 15, офис 503а
        </li>
        <li>
            <span class="text-gray-500">E-mail для обращений по персональным данным:</span>
            <a href="mailto:bisoft@shoil.ru" class="font-medium text-indigo-600 hover:text-indigo-800">bisoft@shoil.ru</a>
        </li>
    </ul>
    <p class="text-xs text-gray-500">
        Наименование Оператора должно совпадать с записью в ЕГРЮЛ; при расхождении исправьте формулировку по выписке.
    </p>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">3. Какие данные обрабатываются</h3>
    <ul class="list-disc space-y-1 pl-5">
        <li>ФИО и адрес электронной почты (при указании), вводимые при прохождении тестирования на Сайте;</li>
        <li>технические данные: IP-адрес, сведения о браузере, файлы cookie, данные сессии — в объёме, необходимом для работы Сайта и безопасности.</li>
    </ul>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">4. Цели и правовые основания обработки</h3>
    <p>Обработка персональных данных осуществляется в целях:</p>
    <ul class="list-disc space-y-1 pl-5">
        <li>организации прохождения тестов и фиксации результатов;</li>
        <li>обеспечения работоспособности и безопасности Сайта (в т.ч. защита от злоупотреблений).</li>
    </ul>
    <p>
        Правовые основания: согласие субъекта персональных данных (ст. 9 152-ФЗ), где применимо;
        исполнение соглашения с пользователем; законные интересы оператора, не нарушающие права субъекта.
    </p>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">5. Сроки и место обработки</h3>
    <p>
        Персональные данные обрабатываются на территории Российской Федерации на серверах, на которых размещён Сайт,
        в течение срока, необходимого для достижения целей обработки, если иной срок не предусмотрен законом или договором.
    </p>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">6. Права субъекта персональных данных</h3>
    <p>Вы вправе запросить уточнение, блокирование или уничтожение ваших персональных данных, отозвать согласие,
        обратиться с жалобой в уполномоченный орган по защите прав субъектов персональных данных (Роскомнадзор),
        в порядке, установленном 152-ФЗ.</p>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">7. Файлы cookie</h3>
    <p>
        Сайт использует cookie для работы сессии и защиты форм. Подробнее —
        @if ($useModalTriggers)
            <button
                type="button"
                class="font-medium text-indigo-600 hover:text-indigo-800"
                @click="legalCookies = true; legalPrivacy = false"
            >«Политика cookie»</button>.
        @else
            <a href="{{ route('legal.cookies') }}" class="font-medium text-indigo-600 hover:text-indigo-800">«Политика cookie»</a>.
        @endif
    </p>
</section>

<section class="space-y-2">
    <h3 class="text-base font-semibold text-gray-900">8. Изменения политики</h3>
    <p>Оператор вправе обновлять настоящую политику. Актуальная версия доступна на Сайте (раздел «Персональные данные») и по адресу {{ url('/privacy') }}.</p>
</section>

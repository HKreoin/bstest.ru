{{-- Родитель: x-data с legalPrivacy, legalCookies --}}
<div
    x-show="legalPrivacy"
    x-cloak
    x-transition.opacity.duration.200ms
    class="fixed inset-0 z-[100] flex items-start justify-center overflow-y-auto bg-gray-900/50 p-4 sm:p-8"
    role="dialog"
    aria-modal="true"
    aria-labelledby="legal-privacy-title"
    @click.self="legalPrivacy = false"
>
    <div class="my-4 w-full max-w-3xl rounded-lg bg-white shadow-xl sm:my-8" @click.stop>
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6">
            <h2 id="legal-privacy-title" class="text-lg font-semibold text-gray-900">
                Политика в отношении обработки персональных данных
            </h2>
            <button
                type="button"
                class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                @click="legalPrivacy = false"
                aria-label="Закрыть"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="max-h-[min(70vh,32rem)] space-y-6 overflow-y-auto px-4 py-4 text-sm text-gray-700 sm:px-6 sm:py-6">
            @include('legal.partials.privacy-body', ['useModalTriggers' => true])
        </div>
    </div>
</div>

<div
    x-show="legalCookies"
    x-cloak
    x-transition.opacity.duration.200ms
    class="fixed inset-0 z-[100] flex items-start justify-center overflow-y-auto bg-gray-900/50 p-4 sm:p-8"
    role="dialog"
    aria-modal="true"
    aria-labelledby="legal-cookies-title"
    @click.self="legalCookies = false"
>
    <div class="my-4 w-full max-w-3xl rounded-lg bg-white shadow-xl sm:my-8" @click.stop>
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6">
            <h2 id="legal-cookies-title" class="text-lg font-semibold text-gray-900">
                Политика использования файлов cookie
            </h2>
            <button
                type="button"
                class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
                @click="legalCookies = false"
                aria-label="Закрыть"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="max-h-[min(70vh,32rem)] space-y-6 overflow-y-auto px-4 py-4 text-sm text-gray-700 sm:px-6 sm:py-6">
            @include('legal.partials.cookies-body', ['useModalTriggers' => true])
        </div>
    </div>
</div>

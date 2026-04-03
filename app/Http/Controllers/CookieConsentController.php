<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CookieConsentController extends Controller
{
    /**
     * Фиксирует согласие с политикой cookie на стороне сервера (HTTP-cookie).
     */
    public function store(Request $request): Response
    {
        $minutes = 60 * 24 * 400;

        return response()->noContent()
            ->cookie(
                'bstest_cookie_consent',
                '1',
                $minutes,
                '/',
                null,
                $request->secure(),
                false,
                false,
                'lax'
            );
    }
}

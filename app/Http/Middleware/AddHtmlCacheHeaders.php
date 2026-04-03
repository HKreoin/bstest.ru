<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Чтобы страницы с баннером cookie не отдавались из кэша без учёта Cookie (устаревший data-cookie-consent).
 */
class AddHtmlCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $request->isMethod('GET') || $request->expectsJson()) {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');

        if (! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'private, no-cache, must-revalidate, max-age=0');

        $existingVary = $response->headers->get('Vary');
        $response->headers->set('Vary', $existingVary ? $existingVary.', Cookie' : 'Cookie');

        return $response;
    }
}

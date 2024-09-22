<?php

namespace App\Middleware;

use Closure;
use Core\Http\Request;
use Core\Http\Respond;
use Core\Middleware\MiddlewareInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        $header = respond()->getHeader();
        $header->set('Access-Control-Allow-Origin', '*'); // Ganti dengan origin yang sesuai
        $header->set('Access-Control-Allow-Origin', 'https://staging.munola.com'); // Ganti dengan origin yang sesuai
        $header->set('Access-Control-Expose-Headers', 'Authorization, Content-Type, Cache-Control, Content-Disposition');

        // Set header Vary
        $vary = $header->has('Vary') ? explode(', ', $header->get('Vary')) : [];
        $vary = array_unique([...$vary, 'Accept', 'Origin', 'User-Agent', 'Access-Control-Request-Method', 'Access-Control-Request-Headers']);
        $header->set('Vary', join(', ', $vary));

        // Handle preflight request
        if ($request->method(Request::OPTIONS)) {
            $header->set(
                'Access-Control-Allow-Methods',
                strtoupper($request->server->get('HTTP_ACCESS_CONTROL_REQUEST_METHOD', 'GET, POST, OPTIONS'))
            );

            $header->set(
                'Access-Control-Allow-Headers',
                $request->server->get('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', 'Origin, Content-Type, Accept, Authorization, Accept-Language')
            );

            return respond()->setCode(Respond::HTTP_NO_CONTENT);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $action = $request->route()?->getName() ?? $request->method().' '.$request->path();

            AuditService::logAction(
                action: $action,
                description: null
            );
        }

        return $response;
    }
}

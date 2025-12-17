<?php

namespace Tapsilat\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tapsilat\Laravel\Facades\Tapsilat;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Tapsilat-Signature', '');

        if (!Tapsilat::verifyWebhook($payload, $signature)) {
            abort(401, 'Invalid webhook signature');
        }

        return $next($request);
    }
}

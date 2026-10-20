<?php

namespace Tapsilat\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\TapsilatAPI;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Tapsilat::verifyWebhook($request->getContent(), static::signature($request))) {
            abort(401, 'Invalid webhook signature');
        }

        return $next($request);
    }

    /**
     * The request's Tapsilat-Signature header, or the X-Tapsilat-Signature header earlier versions read.
     */
    public static function signature(Request $request): string
    {
        return (string) ($request->header(TapsilatAPI::WEBHOOK_SIGNATURE_HEADER)
            ?? $request->header('X-Tapsilat-Signature', ''));
    }
}

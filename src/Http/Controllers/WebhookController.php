<?php

namespace Tapsilat\Laravel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Tapsilat\Laravel\Events\WebhookReceived;
use Tapsilat\Laravel\Events\OrderPaid;
use Tapsilat\Laravel\Events\OrderFailed;
use Tapsilat\Laravel\Events\OrderRefunded;
use Tapsilat\Laravel\Events\SubscriptionCreated;
use Tapsilat\Laravel\Events\SubscriptionCanceled;
use Tapsilat\Laravel\Events\SubscriptionPaymentSucceeded;
use Tapsilat\Laravel\Events\SubscriptionPaymentFailed;
use Tapsilat\Laravel\Facades\Tapsilat;

class WebhookController extends Controller
{
    /**
     * Handle incoming Tapsilat webhook.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Tapsilat-Signature', '');

        // Verify webhook signature
        if (!Tapsilat::verifyWebhook($payload, $signature)) {
            Log::warning('[Tapsilat Webhook] Invalid signature', [
                'signature' => $signature,
            ]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('[Tapsilat Webhook] Invalid JSON payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // Dispatch generic webhook event
        event(new WebhookReceived($data));

        // Dispatch specific events based on event type
        $this->dispatchEventByType($data);

        Log::info('[Tapsilat Webhook] Received', [
            'event' => $data['event'] ?? 'unknown',
            'reference_id' => $data['reference_id'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Dispatch specific event based on webhook type.
     */
    protected function dispatchEventByType(array $data): void
    {
        $eventType = $data['event'] ?? null;

        match ($eventType) {
            'order.paid' => event(new OrderPaid($data)),
            'order.failed' => event(new OrderFailed($data)),
            'order.refunded' => event(new OrderRefunded($data)),
            'subscription.created' => event(new SubscriptionCreated($data)),
            'subscription.canceled' => event(new SubscriptionCanceled($data)),
            'subscription.payment.succeeded' => event(new SubscriptionPaymentSucceeded($data)),
            'subscription.payment.failed' => event(new SubscriptionPaymentFailed($data)),
            default => null,
        };
    }
}

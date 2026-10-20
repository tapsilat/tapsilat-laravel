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
use Tapsilat\Laravel\Http\Middleware\VerifyWebhookSignature;
use Tapsilat\WebhookEvent;

class WebhookController extends Controller
{
    /**
     * Handle incoming Tapsilat webhook.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();

        if (!Tapsilat::verifyWebhook($payload, VerifyWebhookSignature::signature($request))) {
            Log::warning('[Tapsilat Webhook] Invalid signature');

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);

        if (!is_array($data)) {
            Log::error('[Tapsilat Webhook] Invalid JSON payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // Dispatch generic webhook event
        event(new WebhookReceived($data));

        // Dispatch specific events based on the order status
        $this->dispatchEventByType($data);

        $event = new WebhookEvent($data);
        Log::info('[Tapsilat Webhook] Received', [
            'type' => $data['event'] ?? $event->type ?? 'unknown',
            'reference_id' => $event->getReferenceId(),
            'status' => $event->getStatus(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Dispatch the event that matches the callback's order status. Tapsilat callbacks are
     * {"type", "data", "token"} objects; a payload with an "event" field is dispatched by that field.
     */
    protected function dispatchEventByType(array $data): void
    {
        if (isset($data['event']) && is_string($data['event'])) {
            match ($data['event']) {
                'order.paid' => event(new OrderPaid($data)),
                'order.failed' => event(new OrderFailed($data)),
                'order.refunded' => event(new OrderRefunded($data)),
                'subscription.created' => event(new SubscriptionCreated($data)),
                'subscription.canceled' => event(new SubscriptionCanceled($data)),
                'subscription.payment.succeeded' => event(new SubscriptionPaymentSucceeded($data)),
                'subscription.payment.failed' => event(new SubscriptionPaymentFailed($data)),
                default => null,
            };

            return;
        }

        $event = new WebhookEvent($data);

        if ($event->isPaid()) {
            event(new OrderPaid($data));
        } elseif ($event->isFailed()) {
            event(new OrderFailed($data));
        } elseif ($event->isRefunded()) {
            event(new OrderRefunded($data));
        }
    }
}

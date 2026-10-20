<?php

namespace Tapsilat\Laravel\Events\Concerns;

use Tapsilat\WebhookEvent;

/**
 * Reads a Tapsilat callback, {"type": ..., "data": {...}, "token": ...}, whichever callback type the
 * organization chose in the panel. A flat payload, such as one built by hand, is read as it is.
 */
trait ReadsWebhookPayload
{
    /**
     * The callback as the SDK parses it.
     */
    public function webhookEvent(): WebhookEvent
    {
        return new WebhookEvent($this->payload);
    }

    /**
     * The callback type: order_lite, order, order_payment_detail, extended_order or payment_facilitator.
     */
    public function getType(): ?string
    {
        return $this->webhookEvent()->type;
    }

    /**
     * The order's reference ID.
     */
    public function getReferenceId(): ?string
    {
        return $this->stringValue($this->payload['reference_id'] ?? null)
            ?? $this->webhookEvent()->getReferenceId();
    }

    /**
     * The conversation ID the order was created with.
     */
    public function getConversationId(): ?string
    {
        return $this->stringValue($this->payload['conversation_id'] ?? null)
            ?? $this->webhookEvent()->getConversationId();
    }

    /**
     * The order's ID.
     */
    public function getOrderId(): ?string
    {
        if (isset($this->payload['order_id'])) {
            return $this->stringValue($this->payload['order_id']);
        }

        $event = $this->webhookEvent();
        if ($event->type === WebhookEvent::TYPE_ORDER_PAYMENT_DETAIL) {
            return $this->stringValue($event->data['order']['id'] ?? null);
        }

        return $this->stringValue($event->data['id'] ?? null);
    }

    /**
     * The order status name, such as "Paid", when the callback type carries one.
     */
    public function getStatus(): ?string
    {
        return $this->webhookEvent()->getStatus();
    }

    /**
     * The order status code (see Tapsilat\OrderStatus), when the callback type carries one.
     */
    public function getStatusCode(): ?int
    {
        return $this->webhookEvent()->getStatusCode();
    }

    /**
     * A field of the payload, or of its data object.
     */
    protected function payloadValue(string $key): mixed
    {
        return $this->payload[$key] ?? $this->webhookEvent()->data[$key] ?? null;
    }

    protected function stringValue(mixed $value): ?string
    {
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return is_int($value) ? (string) $value : null;
    }
}

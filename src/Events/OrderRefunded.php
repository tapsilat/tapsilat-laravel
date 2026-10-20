<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapsilat\Laravel\Events\Concerns\ReadsWebhookPayload;

/**
 * Dispatched for a verified callback whose order status is Refunded or Partially refunded.
 */
class OrderRefunded
{
    use Dispatchable, SerializesModels, ReadsWebhookPayload;

    /**
     * The webhook payload.
     */
    public array $payload;

    /**
     * Create a new event instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the refunded amount.
     */
    public function getRefundedAmount(): ?float
    {
        $amount = $this->payloadValue('refunded_amount');

        return is_numeric($amount) ? (float) $amount : null;
    }

    /**
     * Get the currency.
     */
    public function getCurrency(): ?string
    {
        return $this->stringValue($this->payloadValue('currency'));
    }
}

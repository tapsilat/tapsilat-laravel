<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionPaymentSucceeded
{
    use Dispatchable, SerializesModels;

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
     * Get the subscription reference ID.
     */
    public function getReferenceId(): ?string
    {
        return $this->payload['reference_id'] ?? null;
    }

    /**
     * Get the order reference ID.
     */
    public function getOrderReferenceId(): ?string
    {
        return $this->payload['order_reference_id'] ?? null;
    }

    /**
     * Get the paid amount.
     */
    public function getAmount(): ?float
    {
        return isset($this->payload['amount']) ? (float) $this->payload['amount'] : null;
    }

    /**
     * Get the currency.
     */
    public function getCurrency(): ?string
    {
        return $this->payload['currency'] ?? null;
    }
}

<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderRefunded
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
     * Get the order reference ID.
     */
    public function getReferenceId(): ?string
    {
        return $this->payload['reference_id'] ?? null;
    }

    /**
     * Get the order ID.
     */
    public function getOrderId(): ?string
    {
        return $this->payload['order_id'] ?? null;
    }

    /**
     * Get the refunded amount.
     */
    public function getRefundedAmount(): ?float
    {
        return isset($this->payload['refunded_amount']) ? (float) $this->payload['refunded_amount'] : null;
    }

    /**
     * Get the currency.
     */
    public function getCurrency(): ?string
    {
        return $this->payload['currency'] ?? null;
    }
}

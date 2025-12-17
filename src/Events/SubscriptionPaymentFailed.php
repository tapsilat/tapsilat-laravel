<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionPaymentFailed
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
     * Get the error code.
     */
    public function getErrorCode(): ?string
    {
        return $this->payload['error_code'] ?? null;
    }

    /**
     * Get the error message.
     */
    public function getErrorMessage(): ?string
    {
        return $this->payload['error_message'] ?? $this->payload['error'] ?? null;
    }
}

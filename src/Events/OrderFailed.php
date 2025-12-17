<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderFailed
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

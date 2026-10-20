<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapsilat\Laravel\Events\Concerns\ReadsWebhookPayload;

/**
 * Dispatched for a verified callback whose order status is Cancelled, Fraud, Rejected, Failure,
 * Expired or Terminated.
 */
class OrderFailed
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
     * Get the error code.
     */
    public function getErrorCode(): ?string
    {
        return $this->stringValue($this->payloadValue('error_code'));
    }

    /**
     * Get the error message.
     */
    public function getErrorMessage(): ?string
    {
        return $this->stringValue($this->payloadValue('error_message'))
            ?? $this->stringValue($this->payloadValue('error'));
    }
}

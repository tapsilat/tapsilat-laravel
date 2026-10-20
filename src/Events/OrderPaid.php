<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapsilat\Laravel\Events\Concerns\ReadsWebhookPayload;

/**
 * Dispatched for a verified callback whose order status is Paid, Completed or Sub merchant payment
 * approved.
 */
class OrderPaid
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
     * Get the paid amount.
     */
    public function getAmount(): ?float
    {
        $amount = $this->payloadValue('amount') ?? $this->payloadValue('paid_amount');

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

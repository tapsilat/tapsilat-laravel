<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tapsilat\Laravel\Events\Concerns\ReadsWebhookPayload;

/**
 * Dispatched for every verified callback, before the status-specific events.
 */
class WebhookReceived
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
     * Get the event type: the payload's "event" field when it has one, else the callback type.
     */
    public function getEventType(): ?string
    {
        return $this->stringValue($this->payload['event'] ?? null) ?? $this->getType();
    }

    /**
     * Get the payload data.
     */
    public function getData(): array
    {
        return is_array($this->payload['data'] ?? null) ? $this->payload['data'] : $this->payload;
    }
}

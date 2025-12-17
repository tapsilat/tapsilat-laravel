<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebhookReceived
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
     * Get the event type from the payload.
     */
    public function getEventType(): ?string
    {
        return $this->payload['event'] ?? null;
    }

    /**
     * Get the reference ID from the payload.
     */
    public function getReferenceId(): ?string
    {
        return $this->payload['reference_id'] ?? null;
    }

    /**
     * Get the payload data.
     */
    public function getData(): array
    {
        return $this->payload['data'] ?? $this->payload;
    }
}

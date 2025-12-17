<?php

namespace Tapsilat\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionCanceled
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
     * Get the external reference ID.
     */
    public function getExternalReferenceId(): ?string
    {
        return $this->payload['external_reference_id'] ?? null;
    }

    /**
     * Get the cancellation reason.
     */
    public function getReason(): ?string
    {
        return $this->payload['reason'] ?? null;
    }
}

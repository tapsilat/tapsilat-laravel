<?php

namespace Tapsilat\Laravel;

use Illuminate\Support\Facades\Log;
use Tapsilat\APIException;
use Tapsilat\Models\BuyerDTO;
use Tapsilat\Models\OrderCreateDTO;
use Tapsilat\Models\OrderResponse;
use Tapsilat\Models\RefundOrderDTO;
use Tapsilat\Models\OrderPaymentTermCreateDTO;
use Tapsilat\Models\OrderPaymentTermUpdateDTO;
use Tapsilat\Models\OrderTermRefundRequest;
use Tapsilat\Models\SubscriptionCreateRequest;
use Tapsilat\Models\SubscriptionGetRequest;
use Tapsilat\Models\SubscriptionCancelRequest;
use Tapsilat\Models\SubscriptionRedirectRequest;
use Tapsilat\Models\SubscriptionCreateResponse;
use Tapsilat\Models\SubscriptionDetail;
use Tapsilat\Models\SubscriptionRedirectResponse;
use Tapsilat\Models\OrderAccountingRequest;
use Tapsilat\Models\OrderPostAuthRequest;
use Tapsilat\TapsilatAPI;

class TapsilatManager
{
    /**
     * The configuration array.
     */
    protected array $config;

    /**
     * The Tapsilat API client instance.
     */
    protected ?TapsilatAPI $client = null;

    /**
     * Create a new Tapsilat manager instance.
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get the Tapsilat API client instance.
     */
    public function client(): TapsilatAPI
    {
        if ($this->client === null) {
            $this->client = new TapsilatAPI(
                $this->config['api_key'] ?? '',
                $this->config['timeout'] ?? 30,
                $this->config['base_url'] ?? 'https://panel.tapsilat.dev/api/v1'
            );
        }

        return $this->client;
    }

    /**
     * Get the configuration value.
     */
    public function getConfig(?string $key = null, $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }

    // =========================================================================
    // Order Methods
    // =========================================================================

    /**
     * Create a new order.
     */
    public function createOrder(OrderCreateDTO $order): OrderResponse
    {
        $this->log('Creating order', ['amount' => $order->amount, 'currency' => $order->currency]);

        try {
            $response = $this->client()->createOrder($order);
            $this->log('Order created successfully', ['reference_id' => $response->getReferenceId()]);
            return $response;
        } catch (APIException $e) {
            $this->logError('Failed to create order', $e);
            throw $e;
        }
    }

    /**
     * Create an order with simplified parameters.
     */
    public function createSimpleOrder(
        float $amount,
        string $buyerName,
        string $buyerSurname,
        string $buyerEmail,
        ?string $currency = null,
        ?string $locale = null,
        ?string $conversationId = null
    ): OrderResponse {
        $buyer = new BuyerDTO($buyerName, $buyerSurname, null, null, null, $buyerEmail);

        $order = new OrderCreateDTO(
            $amount,
            $currency ?? $this->config['default_currency'] ?? 'TRY',
            $locale ?? $this->config['default_locale'] ?? 'tr',
            $buyer,
            null, // basket_items
            null, // billing_address
            null, // checkout_design
            $conversationId
        );

        return $this->createOrder($order);
    }

    /**
     * Get order details by reference ID.
     */
    public function getOrder(string $referenceId): OrderResponse
    {
        $this->log('Getting order', ['reference_id' => $referenceId]);
        return $this->client()->getOrder($referenceId);
    }

    /**
     * Get order details by conversation ID.
     */
    public function getOrderByConversationId(string $conversationId): OrderResponse
    {
        $this->log('Getting order by conversation ID', ['conversation_id' => $conversationId]);
        return $this->client()->getOrderByConversationId($conversationId);
    }

    /**
     * Get a paginated list of orders.
     */
    public function getOrderList(
        int $page = 1,
        int $perPage = 10,
        string $startDate = '',
        string $endDate = '',
        string $organizationId = '',
        string $relatedReferenceId = ''
    ): array {
        return $this->client()->getOrderList($page, $perPage, $startDate, $endDate, $organizationId, $relatedReferenceId);
    }

    /**
     * Get orders with pagination.
     */
    public function getOrders(string $page = '1', string $perPage = '10', string $buyerId = ''): array
    {
        return $this->client()->getOrders($page, $perPage, $buyerId);
    }

    /**
     * Get order submerchants.
     */
    public function getOrderSubmerchants(int $page = 1, int $perPage = 10): array
    {
        return $this->client()->getOrderSubmerchants($page, $perPage);
    }

    /**
     * Get the checkout URL for an order.
     */
    public function getCheckoutUrl(string $referenceId): string
    {
        return $this->client()->getCheckoutUrl($referenceId);
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(string $referenceId): array
    {
        $this->log('Canceling order', ['reference_id' => $referenceId]);
        return $this->client()->cancelOrder($referenceId);
    }

    /**
     * Refund an order.
     */
    public function refundOrder(RefundOrderDTO $refundData): array
    {
        $this->log('Refunding order', [
            'reference_id' => $refundData->reference_id,
            'amount' => $refundData->amount,
        ]);
        return $this->client()->refundOrder($refundData);
    }

    /**
     * Refund an order with simplified parameters.
     */
    public function refundOrderSimple(
        float $amount,
        string $referenceId,
        ?string $orderItemId = null,
        ?string $orderItemPaymentId = null
    ): array {
        $refundData = new RefundOrderDTO($amount, $referenceId, $orderItemId, $orderItemPaymentId);
        return $this->refundOrder($refundData);
    }

    /**
     * Refund all of an order.
     */
    public function refundAllOrder(string $referenceId): array
    {
        $this->log('Refunding all order', ['reference_id' => $referenceId]);
        return $this->client()->refundAllOrder($referenceId);
    }

    /**
     * Get order payment details.
     */
    public function getOrderPaymentDetails(string $referenceId, string $conversationId = ''): array
    {
        return $this->client()->getOrderPaymentDetails($referenceId, $conversationId);
    }

    /**
     * Get order status.
     */
    public function getOrderStatus(string $referenceId): array
    {
        return $this->client()->getOrderStatus($referenceId);
    }

    /**
     * Get order transactions.
     */
    public function getOrderTransactions(string $referenceId): array
    {
        return $this->client()->getOrderTransactions($referenceId);
    }

    /**
     * Process accounting for an order.
     */
    public function orderAccounting(OrderAccountingRequest $request): array
    {
        $this->log('Processing order accounting', ['order_reference_id' => $request->order_reference_id]);

        try {
            return $this->client()->orderAccounting($request);
        } catch (APIException $e) {
            $this->logError('Failed to process order accounting', $e);
            throw $e;
        }
    }

    /**
     * Process post-authorization for an order.
     */
    public function orderPostAuth(OrderPostAuthRequest $request): array
    {
        $this->log('Processing order post-auth', ['reference_id' => $request->reference_id]);

        try {
            return $this->client()->orderPostAuth($request);
        } catch (APIException $e) {
            $this->logError('Failed to process order post-auth', $e);
            throw $e;
        }
    }

    /**
     * Get system order statuses.
     */
    public function getSystemOrderStatuses(): array
    {
        return $this->client()->getSystemOrderStatuses();
    }

    // =========================================================================
    // Order Term Methods
    // =========================================================================

    /**
     * Get order term by reference ID.
     */
    public function getOrderTerm(string $termReferenceId): array
    {
        return $this->client()->getOrderTerm($termReferenceId);
    }

    /**
     * Create an order payment term.
     */
    public function createOrderTerm(OrderPaymentTermCreateDTO $term): array
    {
        $this->log('Creating order term', ['order_id' => $term->order_id]);
        return $this->client()->createOrderTerm($term);
    }

    /**
     * Delete an order payment term.
     */
    public function deleteOrderTerm(string $orderId, string $termReferenceId): array
    {
        $this->log('Deleting order term', [
            'order_id' => $orderId,
            'term_reference_id' => $termReferenceId,
        ]);
        return $this->client()->deleteOrderTerm($orderId, $termReferenceId);
    }

    /**
     * Update an order payment term.
     */
    public function updateOrderTerm(OrderPaymentTermUpdateDTO $term): array
    {
        $this->log('Updating order term', ['term_reference_id' => $term->term_reference_id]);
        return $this->client()->updateOrderTerm($term);
    }

    /**
     * Refund an order term.
     */
    public function refundOrderTerm(OrderTermRefundRequest $term): array
    {
        $this->log('Refunding order term');
        return $this->client()->refundOrderTerm($term);
    }

    /**
     * Terminate an order.
     */
    public function orderTerminate(string $referenceId): array
    {
        $this->log('Terminating order', ['reference_id' => $referenceId]);
        return $this->client()->orderTerminate($referenceId);
    }

    /**
     * Terminate an order term.
     */
    public function terminateOrderTerm(string $termReferenceId, string $reason = ''): array
    {
        $this->log('Terminating order term', [
            'term_reference_id' => $termReferenceId,
            'reason' => $reason,
        ]);
        return $this->client()->terminateOrderTerm($termReferenceId, $reason);
    }

    /**
     * Manual callback for an order.
     */
    public function orderManualCallback(string $referenceId, string $conversationId = ''): array
    {
        $this->log('Manual callback for order', [
            'reference_id' => $referenceId,
            'conversation_id' => $conversationId,
        ]);
        return $this->client()->orderManualCallback($referenceId, $conversationId);
    }

    /**
     * Update order related reference.
     */
    public function orderRelatedUpdate(string $referenceId, string $relatedReferenceId): array
    {
        $this->log('Updating order related reference', [
            'reference_id' => $referenceId,
            'related_reference_id' => $relatedReferenceId,
        ]);
        return $this->client()->orderRelatedUpdate($referenceId, $relatedReferenceId);
    }

    // =========================================================================
    // Subscription Methods
    // =========================================================================

    /**
     * Create a new subscription.
     */
    public function createSubscription(SubscriptionCreateRequest $subscription): SubscriptionCreateResponse
    {
        $this->log('Creating subscription', [
            'amount' => $subscription->amount,
            'title' => $subscription->title,
        ]);

        try {
            $response = $this->client()->createSubscription($subscription);
            $this->log('Subscription created successfully', [
                'reference_id' => $response->getReferenceId(),
            ]);
            return $response;
        } catch (APIException $e) {
            $this->logError('Failed to create subscription', $e);
            throw $e;
        }
    }

    /**
     * Get subscription details.
     */
    public function getSubscription(SubscriptionGetRequest $request): SubscriptionDetail
    {
        return $this->client()->getSubscription($request);
    }

    /**
     * Get subscription by reference ID.
     */
    public function getSubscriptionByReferenceId(string $referenceId): SubscriptionDetail
    {
        $request = new SubscriptionGetRequest($referenceId, null);
        return $this->getSubscription($request);
    }

    /**
     * Get subscription by external reference ID.
     */
    public function getSubscriptionByExternalId(string $externalReferenceId): SubscriptionDetail
    {
        $request = new SubscriptionGetRequest(null, $externalReferenceId);
        return $this->getSubscription($request);
    }

    /**
     * List subscriptions with pagination.
     */
    public function listSubscriptions(int $page = 1, int $perPage = 10): array
    {
        return $this->client()->listSubscriptions($page, $perPage);
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(SubscriptionCancelRequest $request): array
    {
        $this->log('Canceling subscription');
        return $this->client()->cancelSubscription($request);
    }

    /**
     * Cancel subscription by reference ID.
     */
    public function cancelSubscriptionByReferenceId(string $referenceId): array
    {
        $request = new SubscriptionCancelRequest($referenceId, null);
        return $this->cancelSubscription($request);
    }

    /**
     * Cancel subscription by external reference ID.
     */
    public function cancelSubscriptionByExternalId(string $externalReferenceId): array
    {
        $request = new SubscriptionCancelRequest(null, $externalReferenceId);
        return $this->cancelSubscription($request);
    }

    /**
     * Redirect subscription.
     */
    public function redirectSubscription(SubscriptionRedirectRequest $request): SubscriptionRedirectResponse
    {
        return $this->client()->redirectSubscription($request);
    }

    // =========================================================================
    // Organization Methods
    // =========================================================================

    /**
     * Get organization settings.
     */
    public function getOrganizationSettings(): array
    {
        return $this->client()->getOrganizationSettings();
    }

    // =========================================================================
    // Health & Webhook Methods
    // =========================================================================

    /**
     * Check API health.
     */
    public function healthCheck(): array
    {
        return $this->client()->healthCheck();
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhook(string $payload, string $signature, ?string $secret = null): bool
    {
        $secret = $secret ?? $this->config['webhook_secret'] ?? '';
        return TapsilatAPI::verifyWebhook($payload, $signature, $secret);
    }

    // =========================================================================
    // Logging Methods
    // =========================================================================

    /**
     * Log a message if logging is enabled.
     */
    protected function log(string $message, array $context = []): void
    {
        if ($this->config['logging']['enabled'] ?? false) {
            $channel = $this->config['logging']['channel'] ?? 'stack';
            Log::channel($channel)->info("[Tapsilat] {$message}", $context);
        }
    }

    /**
     * Log an error if logging is enabled.
     */
    protected function logError(string $message, APIException $e): void
    {
        if ($this->config['logging']['enabled'] ?? false) {
            $channel = $this->config['logging']['channel'] ?? 'stack';
            Log::channel($channel)->error("[Tapsilat] {$message}", [
                'status_code' => $e->statusCode,
                'code' => $e->code,
                'error' => $e->error,
            ]);
        }
    }
}

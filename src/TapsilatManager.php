<?php

namespace Tapsilat\Laravel;

use Illuminate\Support\Facades\Log;
use Tapsilat\APIException;
use Tapsilat\Models\BuyerDTO;
use Tapsilat\Models\OrderCreateDTO;
use Tapsilat\Models\OrderResponse;
use Tapsilat\Models\RefundOrderDTO;
use Tapsilat\Models\OrderPaymentTermCreateDTO;
use Tapsilat\Models\CancelOrderDTO;
use Tapsilat\Models\OrderPaymentTermUpdateDTO;
use Tapsilat\Models\OrderTermRefundRequest;
use Tapsilat\Models\OrderPaymentDetailDTO;
use Tapsilat\Models\SubscriptionCreateRequest;
use Tapsilat\Models\SubscriptionGetRequest;
use Tapsilat\Models\SubscriptionCancelRequest;
use Tapsilat\Models\SubscriptionRedirectRequest;
use Tapsilat\Models\SubscriptionCreateResponse;
use Tapsilat\Models\SubscriptionDetailResponse;
use Tapsilat\Models\SubscriptionRedirectResponse;
use Tapsilat\Models\OrderAccountingRequest;
use Tapsilat\Models\OrderPostAuthRequest;
use Tapsilat\Models\OrderRelatedReferenceDTO;
use Tapsilat\Models\AddBasketItemRequest;
use Tapsilat\Models\RemoveBasketItemRequest;
use Tapsilat\Models\UpdateBasketItemRequest;
use Tapsilat\Models\UpdateCallbackURLRequest;
use Tapsilat\Models\OrgCreateBusinessRequest;
use Tapsilat\Models\GetUserLimitRequest;
use Tapsilat\Models\SetLimitUserRequest;
use Tapsilat\Models\GetVposRequest;
use Tapsilat\Models\OrgCreateUserReq;
use Tapsilat\Models\OrgUserVerifyReq;
use Tapsilat\Models\OrgUserMobileVerifyReq;
use Tapsilat\Models\RefundAllOrderDTO;
use Tapsilat\Models\TerminateRequest;
use Tapsilat\Models\OrderManualCallbackDTO;
use Tapsilat\Models\OrderPaymentTermDeleteDTO;
use Tapsilat\Models\SplitOrderItemPaymentRequest;
use Tapsilat\Models\SubmerchantUpdateDTO;
use Tapsilat\Models\GetOrderPaymentsRequest;
use Tapsilat\Models\OrderPaymentOptionsUpdateRequest;
use Tapsilat\Models\OrgUserTokenCreateReq;
use Tapsilat\Models\OrderOIPDTO;
use Tapsilat\Models\RefundOrderRequest;
use Tapsilat\Models\SubmerchantCreateDTO;
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
            null, // consents
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
    public function getOrders(string $page = '1', string $perPage = '10', string $buyerId = '', ?int $status = null): array
    {
        return $this->client()->getOrders($page, $perPage, $buyerId, $status);
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
        return $this->client()->cancelOrder(new CancelOrderDTO($referenceId));
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
        return $this->client()->refundAllOrder(new RefundAllOrderDTO($referenceId));
    }

    /**
     * Get order payment details.
     */
    public function getOrderPaymentDetails(string $referenceId, string $conversationId = ''): array
    {
        return $this->client()->getOrderPaymentDetails(new OrderPaymentDetailDTO($referenceId, $conversationId));
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
        return $this->client()->deleteOrderTerm(new OrderPaymentTermDeleteDTO($orderId, $termReferenceId));
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
        return $this->client()->orderTerminate(new TerminateRequest($referenceId));
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
        return $this->client()->orderManualCallback(new OrderManualCallbackDTO($referenceId, $conversationId));
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
        return $this->client()->orderRelatedUpdate(new OrderRelatedReferenceDTO($referenceId, $relatedReferenceId));
    }

    /**
     * Add an item to the order basket.
     */
    public function addBasketItem(AddBasketItemRequest $request): array
    {
        $this->log('Adding basket item', ['order_reference_id' => $request->order_reference_id]);
        return $this->client()->addBasketItem($request);
    }

    /**
     * Remove an item from the order basket.
     */
    public function removeBasketItem(RemoveBasketItemRequest $request): array
    {
        $this->log('Removing basket item', ['order_reference_id' => $request->order_reference_id]);
        return $this->client()->removeBasketItem($request);
    }

    /**
     * Update an item in the order basket.
     */
    public function updateBasketItem(UpdateBasketItemRequest $request): array
    {
        $this->log('Updating basket item', ['order_reference_id' => $request->order_reference_id]);
        return $this->client()->updateBasketItem($request);
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
    public function getSubscription(SubscriptionGetRequest $request): SubscriptionDetailResponse
    {
        return $this->client()->getSubscription($request);
    }

    /**
     * Get subscription by reference ID.
     */
    public function getSubscriptionByReferenceId(string $referenceId): SubscriptionDetailResponse
    {
        $request = new SubscriptionGetRequest($referenceId, null);
        return $this->getSubscription($request);
    }

    /**
     * Get subscription by external reference ID.
     */
    public function getSubscriptionByExternalId(string $externalReferenceId): SubscriptionDetailResponse
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

    /**
     * Get organization callback URLs.
     */
    public function getOrganizationCallback(): array
    {
        return $this->client()->getOrganizationCallback();
    }

    /**
     * Update organization callback URLs.
     */
    public function updateOrganizationCallback(UpdateCallbackURLRequest $request): array
    {
        $this->log('Updating organization callback');
        return $this->client()->updateOrganizationCallback($request);
    }

    /**
     * Create organization business entity.
     */
    public function createOrganizationBusiness(OrgCreateBusinessRequest $request): array
    {
        $this->log('Creating organization business');
        return $this->client()->createOrganizationBusiness($request);
    }

    /**
     * Get organization supported currencies.
     */
    public function getOrganizationCurrencies(): array
    {
        return $this->client()->getOrganizationCurrencies();
    }

    /**
     * Get organization user limit configuration.
     */
    public function getOrganizationLimitUser(GetUserLimitRequest $request): array
    {
        return $this->client()->getOrganizationLimitUser($request);
    }

    /**
     * Set organization user limit configuration.
     */
    public function setOrganizationLimitUser(SetLimitUserRequest $request): array
    {
        $this->log('Setting organization user limit');
        return $this->client()->setOrganizationLimitUser($request);
    }

    /**
     * Get organization transaction limits.
     */
    public function getOrganizationLimits(): array
    {
        return $this->client()->getOrganizationLimits();
    }

    /**
     * List organization virtual POS terminals.
     */
    public function listOrganizationVpos(GetVposRequest $request): array
    {
        return $this->client()->listOrganizationVpos($request);
    }

    /**
     * Get organization metadata.
     */
    public function getOrganizationMeta(string $name): array
    {
        return $this->client()->getOrganizationMeta($name);
    }

    /**
     * Get organization scopes (permissions).
     */
    public function getOrganizationScopes(): array
    {
        return $this->client()->getOrganizationScopes();
    }

    /**
     * List sub-organizations.
     */
    public function getOrganizationSuborganizations(int $page = 1, int $perPage = 10): array
    {
        return $this->client()->getOrganizationSuborganizations($page, $perPage);
    }

    /**
     * Create organization user.
     */
    public function createOrganizationUser(OrgCreateUserReq $request): array
    {
        $this->log('Creating organization user');
        return $this->client()->createOrganizationUser($request);
    }

    /**
     * Verify organization user.
     */
    public function verifyOrganizationUser(OrgUserVerifyReq $request): array
    {
        $this->log('Verify organization user');
        return $this->client()->verifyOrganizationUser($request);
    }

    /**
     * Verify organization user mobile.
     */
    public function verifyOrganizationUserMobile(OrgUserMobileVerifyReq $request): array
    {
        $this->log('Verify organization user mobile');
        return $this->client()->verifyOrganizationUserMobile($request);
    }

    // =========================================================================
    // Health & Webhook Methods
    // =========================================================================

    /**
     * Check API health.
     */
    public function healthCheck(): array
    {
        try {
            $this->client()->getSystemErrorCodes();
            return ['status' => 'UP'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
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
    // Synced Methods from Python/PHP Updates
    // =========================================================================

    public function setDebug($debug)
    {
        return $this->client()->setDebug($debug);
    }

    public function getSystemBasketItemTypes()
    {
        return $this->client()->getSystemBasketItemTypes();
    }

    public function getSystemErrorCodes()
    {
        return $this->client()->getSystemErrorCodes();
    }

    public function getSystemPaymentTermStatuses()
    {
        return $this->client()->getSystemPaymentTermStatuses();
    }

    public function getSystemProductTypes()
    {
        return $this->client()->getSystemProductTypes();
    }

    public function getSystemShortcutTypes()
    {
        return $this->client()->getSystemShortcutTypes();
    }

    public function getSystemTransactionPaymentTypes()
    {
        return $this->client()->getSystemTransactionPaymentTypes();
    }

    public function getSystemTransactionPurposes()
    {
        return $this->client()->getSystemTransactionPurposes();
    }

    public function getSystemTransactionStatuses()
    {
        return $this->client()->getSystemTransactionStatuses();
    }

    public function relatedUpdate(string $id, array $payload)
    {
        return $this->client()->relatedUpdate($id, $payload);
    }

    public function terminateOrder(string $id)
    {
        return $this->client()->terminateOrder($id);
    }

    public function manualCallback(string $id)
    {
        return $this->client()->manualCallback($id);
    }

    public function getOrderPayments(GetOrderPaymentsRequest $request)
    {
        return $this->client()->getOrderPayments($request);
    }

    public function getOrderPdf(string $id)
    {
        return $this->client()->getOrderPdf($id);
    }

    public function getOrderExcel(string $id)
    {
        return $this->client()->getOrderExcel($id);
    }

    public function createOrderRefundRequest(RefundOrderRequest $dto)
    {
        return $this->client()->createOrderRefundRequest($dto);
    }

    public function addOrderOip(OrderOIPDTO $dto)
    {
        return $this->client()->addOrderOip($dto);
    }

    public function getOrderPaymentDetailsById($referenceId)
    {
        return $this->client()->getOrderPaymentDetailsById($referenceId);
    }

    public function updatePaymentOptions(OrderPaymentOptionsUpdateRequest $request)
    {
        return $this->client()->updatePaymentOptions($request);
    }

    public function splitOrderItemPayment(SplitOrderItemPaymentRequest $request)
    {
        return $this->client()->splitOrderItemPayment($request);
    }

    public function orderCallback($id)
    {
        return $this->client()->orderCallback($id);
    }

    public function orderVposQuery($id)
    {
        return $this->client()->orderVposQuery($id);
    }

    public function getOrganizationSuborganizationDetails(string $id)
    {
        return $this->client()->getOrganizationSuborganizationDetails($id);
    }

    public function getOrganizationSuborganizationSubmerchants(string $id)
    {
        return $this->client()->getOrganizationSuborganizationSubmerchants($id);
    }

    public function getOrganizationCurrencyPresets()
    {
        return $this->client()->getOrganizationCurrencyPresets();
    }

    public function createOrganizationUserToken(OrgUserTokenCreateReq $request)
    {
        return $this->client()->createOrganizationUserToken($request);
    }

    public function createSubmerchant(SubmerchantCreateDTO $request)
    {
        return $this->client()->createSubmerchant($request);
    }

    public function getSubmerchant(string $id)
    {
        return $this->client()->getSubmerchant($id);
    }

    public function getSuborganizationBySubmerchant(string $id)
    {
        return $this->client()->getSuborganizationBySubmerchant($id);
    }

    public function updateSubmerchant(string $id, SubmerchantUpdateDTO $request)
    {
        return $this->client()->updateSubmerchant($id, $request);
    }

    public function deleteSubmerchant(string $id)
    {
        return $this->client()->deleteSubmerchant($id);
    }

    public function listSubmerchants(int $page = 1, int $perPage = 10)
    {
        return $this->client()->listSubmerchants($page, $perPage);
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

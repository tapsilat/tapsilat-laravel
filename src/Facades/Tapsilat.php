<?php

namespace Tapsilat\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Tapsilat\Laravel\TapsilatManager;
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
use Tapsilat\Models\SubscriptionDetailResponse;
use Tapsilat\Models\SubscriptionRedirectResponse;
use Tapsilat\TapsilatAPI;

/**
 * @method static TapsilatAPI client()
 * @method static mixed getConfig(string $key = null, $default = null)
 * 
 * Order Methods
 * @method static OrderResponse createOrder(OrderCreateDTO $order)
 * @method static OrderResponse createSimpleOrder(float $amount, string $buyerName, string $buyerSurname, string $buyerEmail, ?string $currency = null, ?string $locale = null, ?string $conversationId = null)
 * @method static OrderResponse getOrder(string $referenceId)
 * @method static OrderResponse getOrderByConversationId(string $conversationId)
 * @method static array getOrderList(int $page = 1, int $perPage = 10, string $startDate = '', string $endDate = '', string $organizationId = '', string $relatedReferenceId = '')
 * @method static array getOrders(string $page = '1', string $perPage = '10', string $buyerId = '', ?int $status = null)
 * @method static array getOrderSubmerchants(int $page = 1, int $perPage = 10)
 * @method static string getCheckoutUrl(string $referenceId)
 * @method static array cancelOrder(string $referenceId)
 * @method static array refundOrder(RefundOrderDTO $refundData)
 * @method static array refundOrderSimple(float $amount, string $referenceId, ?string $orderItemId = null, ?string $orderItemPaymentId = null)
 * @method static array refundAllOrder(string $referenceId)
 * @method static array getOrderPaymentDetails(string $referenceId, string $conversationId = '')
 * @method static array getOrderStatus(string $referenceId)
 * @method static array getOrderTransactions(string $referenceId)
 * 
 * Order Term Methods
 * @method static array getOrderTerm(string $termReferenceId)
 * @method static array createOrderTerm(OrderPaymentTermCreateDTO $term)
 * @method static array deleteOrderTerm(string $orderId, string $termReferenceId)
 * @method static array updateOrderTerm(OrderPaymentTermUpdateDTO $term)
 * @method static array refundOrderTerm(OrderTermRefundRequest $term)
 * @method static array orderTerminate(string $referenceId)
 * @method static array terminateOrderTerm(string $termReferenceId, string $reason = '')
 * @method static array orderManualCallback(string $referenceId, string $conversationId = '')
 * @method static array orderRelatedUpdate(string $referenceId, string $relatedReferenceId)
 * 
 * Subscription Methods
 * @method static SubscriptionCreateResponse createSubscription(SubscriptionCreateRequest $subscription)
 * @method static SubscriptionDetailResponse getSubscription(SubscriptionGetRequest $request)
 * @method static SubscriptionDetailResponse getSubscriptionByReferenceId(string $referenceId)
 * @method static SubscriptionDetailResponse getSubscriptionByExternalId(string $externalReferenceId)
 * @method static array listSubscriptions(int $page = 1, int $perPage = 10)
 * @method static array cancelSubscription(SubscriptionCancelRequest $request)
 * @method static array cancelSubscriptionByReferenceId(string $referenceId)
 * @method static array cancelSubscriptionByExternalId(string $externalReferenceId)
 * @method static SubscriptionRedirectResponse redirectSubscription(SubscriptionRedirectRequest $request)
 * 
 * Organization Methods
 * @method static array getOrganizationSettings()
 * 
 * Health & Webhook Methods
 * @method static array healthCheck()
 * @method static bool verifyWebhook(string $payload, string $signature, ?string $secret = null)
 *
 * @see \Tapsilat\Laravel\TapsilatManager
 */
class Tapsilat extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return TapsilatManager::class;
    }
}

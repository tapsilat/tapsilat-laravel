<?php

namespace Tapsilat\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Tapsilat\Laravel\TapsilatManager;

/**
 * @method static \Tapsilat\TapsilatAPI client()
 * @method static mixed getConfig(?string $key = null, $default = null)
 *
 * Methods the manager does not define are forwarded to the Tapsilat\TapsilatAPI client.
 *
 * Orders
 * @method static \Tapsilat\Models\OrderResponse createOrder(\Tapsilat\Models\OrderCreateDTO $order)
 * @method static \Tapsilat\Models\OrderResponse createSimpleOrder(float $amount, string $buyerName, string $buyerSurname, string $buyerEmail, ?string $currency = null, ?string $locale = null, ?string $conversationId = null)
 * @method static \Tapsilat\Models\OrderResponse getOrder(string $referenceId)
 * @method static \Tapsilat\Models\OrderResponse getOrderByConversationId(string $conversationId)
 * @method static array getOrderList(int $page = 1, int $perPage = 10, string $startDate = '', string $endDate = '', string $organizationId = '', string $relatedReferenceId = '')
 * @method static array getOrders(string $page = '1', string $perPage = '10', string $buyerId = '', ?int $status = null)
 * @method static array getOrderSubmerchants(int $page = 1, int $perPage = 10)
 * @method static ?string getCheckoutUrl(string $referenceId)
 * @method static array cancelOrder(string $referenceId)
 * @method static array refundOrder(\Tapsilat\Models\RefundOrderDTO $refundData)
 * @method static array refundOrderSimple(float $amount, string $referenceId, ?string $orderItemId = null, ?string $orderItemPaymentId = null)
 * @method static array refundAllOrder(string $referenceId)
 * @method static array getOrderPaymentDetails(string $referenceId, string $conversationId = '')
 * @method static array getOrderStatus(string $referenceId)
 * @method static array getOrderTransactions(string $referenceId)
 * @method static array orderAccounting(\Tapsilat\Models\OrderAccountingRequest $request)
 * @method static array orderPostAuth(\Tapsilat\Models\OrderPostAuthRequest $request)
 * @method static array orderTerminate(string $referenceId)
 * @method static array orderManualCallback(string $referenceId, string $conversationId = '')
 * @method static array orderRelatedUpdate(string $referenceId, string $relatedReferenceId)
 * @method static mixed relatedUpdate(string $id, array $payload)
 * @method static mixed terminateOrder(string $id)
 * @method static mixed manualCallback(string $id, ?string $conversationId = null)
 * @method static mixed getOrderPayments(\Tapsilat\Models\GetOrderPaymentsRequest $request)
 * @method static mixed getOrderPdf(string $id)
 * @method static mixed getOrderExcel(string $id)
 * @method static mixed createOrderRefundRequest(\Tapsilat\Models\RefundOrderRequest $dto)
 * @method static mixed addOrderOip(\Tapsilat\Models\OrderOIPDTO $dto)
 * @method static mixed getOrderPaymentDetailsById($referenceId)
 * @method static mixed updatePaymentOptions(\Tapsilat\Models\OrderPaymentOptionsUpdateRequest $request)
 * @method static mixed splitOrderItemPayment(\Tapsilat\Models\SplitOrderItemPaymentRequest $request)
 * @method static mixed orderCallback($id)
 * @method static array getOrderListWithFilter(\Tapsilat\Models\OrderListFilter $filter)
 * @method static array getOrderPaymentDetailsByConversationId($conversationId)
 * @method static array chargeOrder(\Tapsilat\Models\OrderChargeRequest $request)
 * @method static array getAllOrdersPayments() Deprecated.
 *
 * Order terms
 * @method static array getOrderTerm(string $termReferenceId)
 * @method static array createOrderTerm(\Tapsilat\Models\OrderPaymentTermCreateDTO $term)
 * @method static array deleteOrderTerm(string $orderId, string $termReferenceId)
 * @method static array updateOrderTerm(\Tapsilat\Models\OrderPaymentTermUpdateDTO $term)
 * @method static array refundOrderTerm(\Tapsilat\Models\OrderTermRefundRequest $term)
 * @method static mixed getSystemPaymentTermStatuses()
 *
 * Basket items
 * @method static array addBasketItem(\Tapsilat\Models\AddBasketItemRequest $request)
 * @method static array removeBasketItem(\Tapsilat\Models\RemoveBasketItemRequest $request)
 * @method static array updateBasketItem(\Tapsilat\Models\UpdateBasketItemRequest $request)
 * @method static mixed getSystemBasketItemTypes()
 *
 * Subscriptions
 * @method static \Tapsilat\Models\SubscriptionCreateResponse createSubscription(\Tapsilat\Models\SubscriptionCreateRequest $subscription)
 * @method static \Tapsilat\Models\SubscriptionDetailResponse getSubscription(\Tapsilat\Models\SubscriptionGetRequest $request)
 * @method static \Tapsilat\Models\SubscriptionDetailResponse getSubscriptionByReferenceId(string $referenceId)
 * @method static \Tapsilat\Models\SubscriptionDetailResponse getSubscriptionByExternalId(string $externalReferenceId)
 * @method static array listSubscriptions(int $page = 1, int $perPage = 10)
 * @method static array cancelSubscription(\Tapsilat\Models\SubscriptionCancelRequest $request)
 * @method static array cancelSubscriptionByReferenceId(string $referenceId)
 * @method static array cancelSubscriptionByExternalId(string $externalReferenceId)
 * @method static \Tapsilat\Models\SubscriptionRedirectResponse redirectSubscription(\Tapsilat\Models\SubscriptionRedirectRequest $request)
 *
 * Organization
 * @method static array getOrganizationSettings()
 * @method static array getOrganizationCallback()
 * @method static array updateOrganizationCallback(\Tapsilat\Models\UpdateCallbackURLRequest $request)
 * @method static array createOrganizationBusiness(\Tapsilat\Models\OrgCreateBusinessRequest $request)
 * @method static array getOrganizationCurrencies()
 * @method static array getOrganizationLimitUser(\Tapsilat\Models\GetUserLimitRequest $request) Deprecated.
 * @method static array setOrganizationLimitUser(\Tapsilat\Models\SetLimitUserRequest $request) Deprecated.
 * @method static array getOrganizationLimits() Deprecated.
 * @method static array getOrganizationLimitsById(string $id, array $params = [])
 * @method static array getOrganizationMeta(string $name)
 * @method static array getOrganizationScopes()
 * @method static array getOrganizationSuborganizations(int $page = 1, int $perPage = 10)
 * @method static array createOrganizationUser(\Tapsilat\Models\OrgCreateUserReq $request)
 * @method static array verifyOrganizationUser(\Tapsilat\Models\OrgUserVerifyReq $request)
 * @method static array verifyOrganizationUserMobile(\Tapsilat\Models\OrgUserMobileVerifyReq $request)
 * @method static mixed getOrganizationSuborganizationDetails(string $id)
 * @method static mixed getOrganizationCurrencyPresets()
 * @method static mixed createOrganizationUserToken(\Tapsilat\Models\OrgUserTokenCreateReq $request)
 * @method static string resolveCurrencyId(string $reference)
 * @method static array createOrganizationCurrency(\Tapsilat\Models\CreateOrganizationCurrencyPayload $payload)
 * @method static array getOrganizationPartners()
 *
 * Submerchants and settlement
 * @method static mixed getOrganizationSuborganizationSubmerchants(string $id)
 * @method static mixed createSubmerchant(\Tapsilat\Models\SubmerchantCreateDTO $request)
 * @method static mixed getSubmerchant(string $id)
 * @method static mixed getSuborganizationBySubmerchant(string $id)
 * @method static mixed updateSubmerchant(string $id, \Tapsilat\Models\SubmerchantUpdateDTO $request)
 * @method static mixed deleteSubmerchant(string $id)
 * @method static mixed listSubmerchants(int $page = 1, int $perPage = 10)
 * @method static array approveSubmerchantPayment(\Tapsilat\Models\SubmerchantPaymentAction $request)
 * @method static array disapproveSubmerchantPayment(\Tapsilat\Models\SubmerchantPaymentAction $request)
 * @method static array updateSubmerchantPaymentItem(\Tapsilat\Models\SubmerchantPaymentItemUpdate $request)
 * @method static array recordSubmerchantPayoutEvent(\Tapsilat\Models\SubmerchantPayoutEventRequest $request)
 *
 * Card installments and saved cards
 * @method static array getCardInstallments(\Tapsilat\Models\CardInstallmentsRequest $request)
 * @method static array getInstallmentTable(\Tapsilat\Models\InstallmentTableRequest $request)
 * @method static array tokenizeCard(\Tapsilat\Models\CardTokenizeRequest $request)
 * @method static array listSavedCards($page = 1, $perPage = 10)
 * @method static array deleteSavedCard($id)
 *
 * Virtual POS
 * @method static array listOrganizationVpos(\Tapsilat\Models\GetVposRequest $request)
 * @method static mixed orderVposQuery($id)
 * @method static array listVpos($page = 1, $perPage = 10, $suborganizationId = null)
 * @method static array createVpos(\Tapsilat\Models\VposCreateRequest $request)
 * @method static array getVpos($id)
 * @method static array updateVpos($id, \Tapsilat\Models\VposCreateRequest $request)
 * @method static array deleteVpos($id)
 * @method static array listVposAcquirers()
 * @method static array listCardSchemes()
 * @method static array listVposAcquirerTemplates()
 * @method static array listVposSubmerchants($page = 1, $perPage = 10, $vposId = null, $externalReferenceId = null)
 * @method static array createVposSubmerchant(\Tapsilat\Models\VposSubmerchantCreateRequest $request)
 * @method static array getVposSubmerchant($id)
 * @method static array updateVposSubmerchant($id, \Tapsilat\Models\VposSubmerchantUpdateRequest $request)
 * @method static array deleteVposSubmerchant($id)
 *
 * Marketplace (API v2)
 * @method static array createMarketplaceSubmerchant(\Tapsilat\Models\MarketplaceSubmerchantCreateRequest $request)
 * @method static array listMarketplaceSubmerchants($page = 1, $perPage = 20, $externalId = null)
 * @method static array getMarketplaceSubmerchant($id)
 * @method static array updateMarketplaceSubmerchant($id, \Tapsilat\Models\MarketplaceProfileUpdate $request)
 * @method static array getMarketplaceSellerContracts(\Tapsilat\Models\MarketplaceContractFilter $filter)
 * @method static array getMarketplaceSellerAccounts($id)
 * @method static array runMarketplaceSellerAccountAction($id, $vposId, \Tapsilat\Models\MarketplaceAccountAction $request)
 * @method static array listMarketplacePayouts(\Tapsilat\Models\MarketplacePayoutFilter $filter)
 * @method static array getMarketplacePayout($id)
 * @method static array approveMarketplacePayouts(\Tapsilat\Models\MarketplacePayoutApproval $request)
 * @method static array runMarketplacePayoutOperation($payoutId, \Tapsilat\Models\MarketplacePayoutOperationInput $request)
 * @method static array getMarketplacePayoutOperation($id)
 * @method static array reconcileMarketplacePayoutOperation($id)
 * @method static array retryMarketplacePayoutOperation($id)
 * @method static array getMarketplaceApprovalSettings()
 * @method static array updateMarketplaceApprovalSettings(\Tapsilat\Models\MarketplaceApprovalSettings $request)
 * @method static array listMarketplaceApprovalPolicies()
 * @method static array getMarketplaceApprovalPolicy($id)
 * @method static array createMarketplaceApprovalPolicy(\Tapsilat\Models\MarketplaceApprovalPolicyInput $request)
 * @method static array updateMarketplaceApprovalPolicy($id, \Tapsilat\Models\MarketplaceApprovalPolicyInput $request)
 * @method static array deleteMarketplaceApprovalPolicy($id)
 * @method static array listMarketplaceReleasePolicies()
 * @method static array getMarketplaceReleasePolicy($id)
 * @method static array createMarketplaceReleasePolicy(\Tapsilat\Models\MarketplaceReleasePolicyInput $request)
 * @method static array updateMarketplaceReleasePolicy($id, \Tapsilat\Models\MarketplaceReleasePolicyInput $request)
 * @method static array deleteMarketplaceReleasePolicy($id)
 *
 * System
 * @method static array getSystemOrderStatuses()
 * @method static mixed getSystemErrorCodes()
 * @method static mixed getSystemProductTypes()
 * @method static mixed getSystemShortcutTypes()
 * @method static mixed getSystemTransactionPaymentTypes()
 * @method static mixed getSystemTransactionPurposes()
 * @method static mixed getSystemTransactionStatuses()
 * @method static array getSystemConfig()
 *
 * Health and webhooks
 * @method static array healthCheck()
 * @method static bool verifyWebhook(string $payload, string $signature, ?string $secret = null, ?int $tolerance = null)
 * @method static mixed setDebug($debug)
 *
 * @see \Tapsilat\Laravel\TapsilatManager
 * @see \Tapsilat\TapsilatAPI
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

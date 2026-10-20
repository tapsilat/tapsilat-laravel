# Tapsilat Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tapsilat/tapsilat-laravel.svg?style=flat-square)](https://packagist.org/packages/tapsilat/tapsilat-laravel)
[![Tests](https://github.com/tapsilat/tapsilat-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/tapsilat/tapsilat-laravel/actions/workflows/tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/tapsilat/tapsilat-laravel.svg?style=flat-square)](https://packagist.org/packages/tapsilat/tapsilat-laravel)

Laravel integration package for the Tapsilat Payment Gateway. This package provides a seamless integration with the [Tapsilat PHP SDK](https://github.com/tapsilat/tapsilat-php) for Laravel applications.

## Requirements

- PHP 8.2+
- Laravel 10.x or 11.x

## Installation

Install the package via Composer:

```bash
composer require tapsilat/tapsilat-laravel
```

### Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="tapsilat-config"
```

Or run the install command which will publish the config and add environment variables:

```bash
php artisan tapsilat:install
```

## Configuration

Add the following environment variables to your `.env` file:

```env
TAPSILAT_API_KEY=your_api_key_here
TAPSILAT_WEBHOOK_SECRET=your_webhook_secret_here

# Optional settings
TAPSILAT_BASE_URL=https://panel.tapsilat.dev/api/v1
TAPSILAT_TIMEOUT=30
TAPSILAT_DEFAULT_CURRENCY=TRY
TAPSILAT_DEFAULT_LOCALE=tr
TAPSILAT_PAYMENT_SUCCESS_URL=https://yoursite.com/payment/success
TAPSILAT_PAYMENT_FAILURE_URL=https://yoursite.com/payment/failure
TAPSILAT_LOGGING_ENABLED=false
```

## Usage

### Using the Facade

```php
use Tapsilat\Laravel\Facades\Tapsilat;

// Create a simple order
$order = Tapsilat::createSimpleOrder(
    amount: 100.00,
    buyerName: 'John',
    buyerSurname: 'Doe',
    buyerEmail: 'john@example.com'
);

// Get checkout URL
$checkoutUrl = $order->getCheckoutUrl();

// Redirect user to checkout
return redirect($checkoutUrl);
```

### Using Dependency Injection

```php
use Tapsilat\Laravel\TapsilatManager;

class PaymentController extends Controller
{
    public function __construct(
        private TapsilatManager $tapsilat
    ) {}

    public function createPayment(Request $request)
    {
        $order = $this->tapsilat->createSimpleOrder(
            100.00,
            'John',
            'Doe',
            'john@example.com'
        );

        return redirect($order->getCheckoutUrl());
    }
}
```

### Creating Orders with Full Options

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\BuyerDTO;
use Tapsilat\Models\OrderCreateDTO;
use Tapsilat\Models\BasketItemDTO;
use Tapsilat\Models\BillingAddressDTO;

// Create buyer
$buyer = new BuyerDTO(
    name: 'John',
    surname: 'Doe',
    email: 'john@example.com',
    gsm_number: '+905551234567'
);

// Create basket items
$basketItem = new BasketItemDTO(
    category1: 'Electronics',
    category2: 'Phones',
    id: 'ITEM-001',
    item_type: 'PHYSICAL',
    name: 'iPhone 15 Pro',
    price: 50000.00,
    quantity: 1
);

// Create billing address
$billingAddress = new BillingAddressDTO(
    address: '123 Main St',
    city: 'Istanbul',
    contact_name: 'John Doe',
    country: 'TR',
    zip_code: '34000'
);

// Create order DTO
$orderDto = new OrderCreateDTO(
    amount: 50000.00,
    currency: 'TRY',
    locale: 'tr',
    buyer: $buyer,
    basket_items: [$basketItem],
    billing_address: $billingAddress,
    conversation_id: 'unique-conversation-id',
    payment_success_url: 'https://yoursite.com/success',
    payment_failure_url: 'https://yoursite.com/failure'
);

$order = Tapsilat::createOrder($orderDto);

return redirect($order->getCheckoutUrl());
```

### Order Operations

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\RefundOrderDTO;

// Get order by reference ID
$order = Tapsilat::getOrder('order-reference-id');

// Get order by conversation ID
$order = Tapsilat::getOrderByConversationId('conversation-id');

// Get order list with pagination
$orders = Tapsilat::getOrderList(page: 1, perPage: 10);

// Cancel an order
Tapsilat::cancelOrder('order-reference-id');

// Refund an order (partial)
Tapsilat::refundOrderSimple(
    amount: 50.00,
    referenceId: 'order-reference-id'
);

// Refund entire order
Tapsilat::refundAllOrder('order-reference-id');

// Get order status
$status = Tapsilat::getOrderStatus('order-reference-id');

// Get order transactions
$transactions = Tapsilat::getOrderTransactions('order-reference-id');

// Get system order statuses
$statuses = Tapsilat::getSystemOrderStatuses();

// Process order accounting
use Tapsilat\Models\OrderAccountingRequest;
$accountingRequest = new OrderAccountingRequest('order-reference-id');
Tapsilat::orderAccounting($accountingRequest);

// Process order post-authorization
use Tapsilat\Models\OrderPostAuthRequest;
$postAuthRequest = new OrderPostAuthRequest(100.00, 'order-reference-id');
Tapsilat::orderPostAuth($postAuthRequest);
```

### Charging, Filtering and Payment Details

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\GetOrderPaymentsRequest;
use Tapsilat\Models\OrderChargeRequest;
use Tapsilat\Models\OrderListFilter;

// Charge an order with a saved card; the charge response keys are camelCase
$result = Tapsilat::chargeOrder(new OrderChargeRequest('order-reference-id'));
if (empty($result['isSuccess'])) {
    // $result['errorCode'], $result['message']
}

// List orders with any of the panel's filters (status is the numeric order status)
$orders = Tapsilat::getOrderListWithFilter(new OrderListFilter([
    'page' => 1, 'per_page' => 50, 'buyer_id' => 'buyer-1', 'status' => 3,
]));

// Payment details by reference ID or by conversation ID
$details = Tapsilat::getOrderPaymentDetails('order-reference-id');
$details = Tapsilat::getOrderPaymentDetailsByConversationId('conversation-id');

// Payments of one order (arguments: order ID, conversation ID, order reference ID; set one)
$payments = Tapsilat::getOrderPayments(new GetOrderPaymentsRequest(null, null, 'order-reference-id'));
```

Payment success and failure URLs set with `TAPSILAT_PAYMENT_SUCCESS_URL` and `TAPSILAT_PAYMENT_FAILURE_URL` apply to every order that does not set its own. Tapsilat posts a signed callback to them when a payment succeeds or fails.

### Subscription Operations

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\SubscriptionCreateRequest;
use Tapsilat\Models\SubscriptionBillingDTO;
use Tapsilat\Models\SubscriptionUserDTO;

// Create billing information
$billing = new SubscriptionBillingDTO(
    address: '123 Main St',
    city: 'Istanbul',
    contact_name: 'John Doe',
    country: 'TR',
    zip_code: '34000'
);

// Create user information
$user = new SubscriptionUserDTO(
    id: 'user-123',
    first_name: 'John',
    last_name: 'Doe',
    email: 'john@example.com',
    phone: '5551234567'
);

// Create subscription request
$subscription = new SubscriptionCreateRequest(
    amount: 99.99,
    currency: 'TRY',
    title: 'Monthly Premium Plan',
    period: 30,
    cycle: 1,
    payment_date: 1,
    external_reference_id: 'sub-ext-123',
    success_url: 'https://yoursite.com/subscription/success',
    failure_url: 'https://yoursite.com/subscription/failure',
    billing: $billing,
    user: $user
);

$response = Tapsilat::createSubscription($subscription);
echo "Subscription Reference: " . $response->getReferenceId();

// Get subscription by reference ID
$subscription = Tapsilat::getSubscriptionByReferenceId('sub-reference-id');

// Get subscription by external ID
$subscription = Tapsilat::getSubscriptionByExternalId('sub-ext-123');

// List subscriptions
$subscriptions = Tapsilat::listSubscriptions(page: 1, perPage: 10);

// Cancel subscription
Tapsilat::cancelSubscriptionByReferenceId('sub-reference-id');
```

### Submerchant Operations

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\SubmerchantCreateDTO;
use Tapsilat\Models\SubmerchantPaymentAction;
use Tapsilat\Models\SubmerchantPaymentItemUpdate;
use Tapsilat\Models\SubmerchantPayoutEventRequest;

// PERSONAL submerchants need identity_number; PRIVATE_COMPANY and
// LIMITED_OR_JOINT_STOCK_COMPANY ones need tax_number.
$dto = new SubmerchantCreateDTO(
    address: 'Sample Address',
    city: 'Istanbul',
    country: 'Turkey',
    email: 'submerchant@example.com',
    gsm_number: '+905551234567',
    iban: 'TR120000000000000000000000',
    identity_number: '11111111111',
    name: 'Sample Submerchant',
    contact_name: 'Jane',
    contact_surname: 'Doe',
    sub_merchant_type: 'PERSONAL',
    tax_office: 'Kadikoy',
    zip_code: '34000',
    locale: 'tr',
    conversation_id: 'submerchant-conversation-id'
);

// Create, read and list submerchants
Tapsilat::createSubmerchant($dto);
$details = Tapsilat::getSubmerchant('submerchant-id');
$submerchants = Tapsilat::listSubmerchants(page: 1, perPage: 10);

// Approve or disapprove a submerchant payment, or change its share
$action = new SubmerchantPaymentAction([
    'locale' => 'tr', 'conversation_id' => 'conv-1', 'payment_transaction_id' => 'tx-1', 'acquirer' => 'acquirer',
]);
Tapsilat::approveSubmerchantPayment($action);
Tapsilat::disapproveSubmerchantPayment($action);
Tapsilat::updateSubmerchantPaymentItem(new SubmerchantPaymentItemUpdate([
    'locale' => 'tr', 'conversation_id' => 'conv-1', 'payment_transaction_id' => 'tx-1',
    'sub_merchant_key' => 'key-1', 'sub_merchant_price' => '60.25', 'acquirer' => 'acquirer',
]));

// Record a payout event, such as a delivery
Tapsilat::recordSubmerchantPayoutEvent(new SubmerchantPayoutEventRequest([
    'idempotency_key' => 'event-1', 'order_reference' => 'order-reference-id', 'item_id' => 'item-1', 'event_type' => 'DELIVERED',
]));
```

### Card Installments and Saved Cards

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\CardInstallmentsRequest;
use Tapsilat\Models\CardTokenizeRequest;
use Tapsilat\Models\InstallmentTableRequest;

$options = Tapsilat::getCardInstallments(new CardInstallmentsRequest([
    'bin' => '54066200', 'amount' => 1000, 'currency' => 'TRY', 'is_three_d' => true,
]));
$table = Tapsilat::getInstallmentTable(new InstallmentTableRequest(['amount' => 1000, 'currency' => 'TRY']));

$card = Tapsilat::tokenizeCard(new CardTokenizeRequest([
    'card_number' => '4111111111111111', 'holder_name' => 'John Doe',
    'expiry_month' => '12', 'expiry_year' => '30', 'cvv' => '123',
]));
$cards = Tapsilat::listSavedCards(1, 10);
Tapsilat::deleteSavedCard('card-id');
```

### Virtual POS

A VPOS update replaces the whole configuration (fields left out are stored as empty or false), so read it first and send every field back.

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\VposUpdateRequest;

$vpos = Tapsilat::getVpos('vpos-id');
$fields = array_intersect_key($vpos, get_class_vars(VposUpdateRequest::class));
Tapsilat::updateVpos('vpos-id', new VposUpdateRequest(array_merge($fields, ['failover' => true])));

$list = Tapsilat::listVpos(1, 10);
$acquirers = Tapsilat::listVposAcquirers();
$submerchants = Tapsilat::listVposSubmerchants(1, 10, 'vpos-id');
```

### Marketplace (API v2)

Marketplace calls use the `/api/v2` URL derived from `TAPSILAT_BASE_URL`. Seller and payout revisions are opaque strings: send them back exactly as received.

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\MarketplaceApprovalPolicyInput;
use Tapsilat\Models\MarketplacePayoutApproval;
use Tapsilat\Models\MarketplacePayoutFilter;
use Tapsilat\Models\MarketplaceProfileUpdate;

$seller = Tapsilat::getMarketplaceSubmerchant('seller-id');
Tapsilat::updateMarketplaceSubmerchant('seller-id', new MarketplaceProfileUpdate([
    'name' => 'New name', 'revision' => $seller['revision'],
]));

$payouts = Tapsilat::listMarketplacePayouts(new MarketplacePayoutFilter(['status' => 'WAITING_APPROVAL', 'page' => 1]));
Tapsilat::approveMarketplacePayouts(new MarketplacePayoutApproval([
    'ids' => array_column($payouts['rows'], 'id'),
]));

// The approval policy currency must be a UUID; a unit such as "TRY" is resolved for you
Tapsilat::createMarketplaceApprovalPolicy(new MarketplaceApprovalPolicyInput([
    'name' => 'Large orders', 'priority' => 1, 'min_amount' => '1000', 'currency_id' => 'TRY', 'mode' => 'manual',
]));
```

### Currencies

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\CreateOrganizationCurrencyPayload;

$currencies = Tapsilat::getOrganizationCurrencies();
Tapsilat::createOrganizationCurrency(new CreateOrganizationCurrencyPayload('EUR'));

// The currency UUID for a unit such as "TRY"; a UUID is returned as it is
$currencyId = Tapsilat::resolveCurrencyId('TRY');
```

### Organization Settings

```php
use Tapsilat\Laravel\Facades\Tapsilat;

$settings = Tapsilat::getOrganizationSettings();
```

### Health Check

```php
use Tapsilat\Laravel\Facades\Tapsilat;

$health = Tapsilat::healthCheck();

// Or use artisan command
// php artisan tapsilat:health
```

## Webhook Handling

The package automatically registers a webhook endpoint at `/tapsilat/webhook` when you have configured `TAPSILAT_WEBHOOK_SECRET`. Use the webhook secret shown in your Tapsilat panel's callback settings, and enter `https://yoursite.com/tapsilat/webhook` as your callback URLs there.

Tapsilat sends each callback as `{"type": "...", "data": {...}, "token": "..."}` with a `Tapsilat-Signature: t=<unix time>,v1=<hex HMAC-SHA256>` header. The endpoint verifies the signature against the secret, rejects callbacks whose timestamp is more than `TAPSILAT_WEBHOOK_TOLERANCE` seconds (default 300) from the server clock, and answers `401` to anything it cannot verify.

### Available Events

Every verified callback dispatches `WebhookReceived`. Then, depending on the order status the callback carries:

| Event | Order status |
|-------|--------------|
| `OrderPaid` | Paid, Completed, Sub merchant payment approved |
| `OrderFailed` | Cancelled, Fraud, Rejected, Failure, Expired, Terminated |
| `OrderRefunded` | Refunded, Partially refunded |

The callback types `order_lite` (the default), `order` and `order_payment_detail` carry a status; `extended_order` and `payment_facilitator` callbacks dispatch only `WebhookReceived`. Every event has `getReferenceId()`, `getConversationId()`, `getOrderId()`, `getType()`, `getStatus()` and `getStatusCode()`, whichever callback type your organization uses.

The `Subscription*` events are dispatched only for payloads with an `event` field such as `subscription.created`; Tapsilat's own callbacks are order callbacks, so a subscription payment arrives as `OrderPaid` for its order.

```php
use Tapsilat\Laravel\Events\OrderPaid;
use Tapsilat\Laravel\Events\OrderFailed;
use Tapsilat\Laravel\Events\OrderRefunded;
use Tapsilat\Laravel\Events\WebhookReceived;
```

### Creating Event Listeners

```php
// app/Listeners/HandleOrderPaid.php
namespace App\Listeners;

use Tapsilat\Laravel\Events\OrderPaid;

class HandleOrderPaid
{
    public function handle(OrderPaid $event): void
    {
        $referenceId = $event->getReferenceId();
        $conversationId = $event->getConversationId();

        // Update your order status
        // Send confirmation email
        // etc.
    }
}
```

Callbacks can be delivered more than once, so make listeners idempotent. To act only on the authoritative status, you can also look it up with `Tapsilat::getOrderStatus($event->getReferenceId())`.

### Registering Listeners

In your `EventServiceProvider`:

```php
use Tapsilat\Laravel\Events\OrderPaid;
use Tapsilat\Laravel\Events\OrderFailed;
use App\Listeners\HandleOrderPaid;
use App\Listeners\HandleOrderFailed;

protected $listen = [
    OrderPaid::class => [
        HandleOrderPaid::class,
    ],
    OrderFailed::class => [
        HandleOrderFailed::class,
    ],
];
```

### Manual Webhook Verification

If you need to handle webhooks in your own route:

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\WebhookEvent;

$payload = request()->getContent();
$signature = request()->header('Tapsilat-Signature', '');

if (Tapsilat::verifyWebhook($payload, $signature)) {
    $event = WebhookEvent::fromPayload($payload);
    $event->getReferenceId();
    $event->isPaid();
}
```

You can also protect your own route with the `Tapsilat\Laravel\Http\Middleware\VerifyWebhookSignature` middleware.

## Accessing the Raw API Client

Every `Tapsilat\TapsilatAPI` method is available on the facade: methods the manager does not define are forwarded to the client. You can also use the client directly:

```php
use Tapsilat\Laravel\Facades\Tapsilat;

$client = Tapsilat::client(); // Tapsilat\TapsilatAPI

$pdf = $client->getOrderPdf('order-reference-id');
```

## Error Handling

```php
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\APIException;

try {
    $order = Tapsilat::createSimpleOrder(100, 'John', 'Doe', 'john@example.com');
} catch (APIException $e) {
    // Handle Tapsilat API errors
    $statusCode = $e->statusCode;
    $errorCode = $e->code;
    $errorMessage = $e->error;

    Log::error('Tapsilat API Error', [
        'status_code' => $statusCode,
        'code' => $errorCode,
        'error' => $errorMessage,
    ]);
}
```

## Logging

Enable logging in your `.env` file:

```env
TAPSILAT_LOGGING_ENABLED=true
TAPSILAT_LOG_CHANNEL=stack
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to support@tapsilat.dev.

## Credits

- [Tapsilat](https://github.com/tapsilat)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

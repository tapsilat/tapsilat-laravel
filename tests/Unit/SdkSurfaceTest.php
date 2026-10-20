<?php

use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Laravel\TapsilatManager;
use Tapsilat\Models\BuyerDTO;
use Tapsilat\Models\CardInstallmentsRequest;
use Tapsilat\Models\CardTokenizeRequest;
use Tapsilat\Models\InstallmentTableRequest;
use Tapsilat\Models\MarketplaceApprovalPolicyInput;
use Tapsilat\Models\MarketplacePayoutApproval;
use Tapsilat\Models\MarketplacePayoutFilter;
use Tapsilat\Models\MarketplaceProfileUpdate;
use Tapsilat\Models\OrderCreateDTO;
use Tapsilat\Models\OrderListFilter;
use Tapsilat\Models\OrderResponse;
use Tapsilat\Models\SubmerchantCreateDTO;
use Tapsilat\Models\SubmerchantPaymentAction;
use Tapsilat\Models\SubmerchantPaymentItemUpdate;
use Tapsilat\Models\SubmerchantPayoutEventRequest;
use Tapsilat\TapsilatAPI;

// SDK methods that are not API calls, or that the manager covers in its own way.
const SDK_INTERNAL_METHODS = ['__construct', 'buildQuery', 'versionedBaseUrl', 'verifyWebhook', 'setDebug'];

/**
 * @return ReflectionMethod[] The public SDK methods the facade exposes
 */
function sdkApiMethods(): array
{
    return array_values(array_filter(
        (new ReflectionClass(TapsilatAPI::class))->getMethods(ReflectionMethod::IS_PUBLIC),
        fn (ReflectionMethod $m) => !$m->isStatic() && !in_array($m->getName(), SDK_INTERNAL_METHODS, true)
    ));
}

/**
 * An argument for a parameter: an uninitialized instance for a class type, else a string.
 */
function dummyArgument(ReflectionParameter $parameter): mixed
{
    $type = $parameter->getType();
    if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
        return (new ReflectionClass($type->getName()))->newInstanceWithoutConstructor();
    }

    return 'x';
}

beforeEach(function () {
    $this->manager = app(TapsilatManager::class);
    $this->apiMock = Mockery::mock(TapsilatAPI::class);

    $property = (new ReflectionClass($this->manager))->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($this->manager, $this->apiMock);
});

afterEach(function () {
    Mockery::close();
});

test('every SDK method the manager does not wrap is forwarded to the client', function () {
    $forwarded = 0;
    foreach (sdkApiMethods() as $method) {
        if (method_exists(TapsilatManager::class, $method->getName())) {
            continue;
        }
        $arguments = array_map('dummyArgument', array_filter(
            $method->getParameters(),
            fn (ReflectionParameter $p) => !$p->isOptional()
        ));
        $this->apiMock->shouldReceive($method->getName())->once()->with(...$arguments)->andReturn(['forwarded' => true]);

        expect(Tapsilat::{$method->getName()}(...$arguments))->toBe(['forwarded' => true]);
        $forwarded++;
    }

    expect($forwarded)->toBeGreaterThan(50);
});

test('the facade documents every manager and SDK method', function () {
    $doc = (string) (new ReflectionClass(Tapsilat::class))->getDocComment();
    preg_match_all('/@method static \S+ (\w+)\(/', $doc, $matches);
    $documented = $matches[1];

    $managerMethods = array_map(
        fn (ReflectionMethod $m) => $m->getName(),
        array_filter(
            (new ReflectionClass(TapsilatManager::class))->getMethods(ReflectionMethod::IS_PUBLIC),
            fn (ReflectionMethod $m) => $m->getDeclaringClass()->getName() === TapsilatManager::class
                && !in_array($m->getName(), ['__construct', '__call'], true)
        )
    );
    $sdkMethods = array_map(fn (ReflectionMethod $m) => $m->getName(), sdkApiMethods());

    expect(array_values(array_diff(array_merge($managerMethods, $sdkMethods), $documented)))->toBe([]);
    expect(array_values(array_diff($documented, array_merge($managerMethods, $sdkMethods))))->toBe([]);
});

test('createOrder applies the configured payment URLs when the order has none', function () {
    config()->set('tapsilat.payment_success_url', 'https://shop.test/paid');
    config()->set('tapsilat.payment_failure_url', 'https://shop.test/failed');
    app()->forgetInstance(TapsilatManager::class);
    $manager = app(TapsilatManager::class);
    $property = (new ReflectionClass($manager))->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($manager, $this->apiMock);

    $this->apiMock->shouldReceive('createOrder')->twice()->andReturnUsing(function (OrderCreateDTO $order) {
        return new OrderResponse(['reference_id' => 'ref-1', 'payment_success_url' => $order->payment_success_url,
            'payment_failure_url' => $order->payment_failure_url]);
    });

    $buyer = new BuyerDTO('Ada', 'Lovelace');
    $defaulted = $manager->createOrder(new OrderCreateDTO(10, 'TRY', 'tr', $buyer));
    $own = new OrderCreateDTO(10, 'TRY', 'tr', $buyer);
    $own->payment_success_url = 'https://shop.test/own';
    $kept = $manager->createOrder($own);

    expect($defaulted->getData()['payment_success_url'])->toBe('https://shop.test/paid');
    expect($defaulted->getData()['payment_failure_url'])->toBe('https://shop.test/failed');
    expect($kept->getData()['payment_success_url'])->toBe('https://shop.test/own');
    expect($kept->getData()['payment_failure_url'])->toBe('https://shop.test/failed');
});

test('an empty conversation ID is left out of payment details and manual callbacks', function () {
    $this->apiMock->shouldReceive('getOrderPaymentDetails')->once()
        ->withArgs(fn ($request) => $request->toArray() === ['reference_id' => 'ref-1'])
        ->andReturn([]);
    $this->apiMock->shouldReceive('orderManualCallback')->once()
        ->withArgs(fn ($request) => $request->toArray() === ['reference_id' => 'ref-1'])
        ->andReturn([]);

    $this->manager->getOrderPaymentDetails('ref-1');
    $this->manager->orderManualCallback('ref-1');
});

test('the README request examples build valid models', function () {
    $models = [
        new OrderListFilter(['page' => 1, 'per_page' => 50, 'buyer_id' => 'buyer-1', 'status' => 3]),
        new SubmerchantCreateDTO(
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
        ),
        new SubmerchantPaymentAction([
            'locale' => 'tr', 'conversation_id' => 'conv-1', 'payment_transaction_id' => 'tx-1', 'acquirer' => 'acquirer',
        ]),
        new SubmerchantPaymentItemUpdate([
            'locale' => 'tr', 'conversation_id' => 'conv-1', 'payment_transaction_id' => 'tx-1',
            'sub_merchant_key' => 'key-1', 'sub_merchant_price' => '60.25', 'acquirer' => 'acquirer',
        ]),
        new SubmerchantPayoutEventRequest([
            'idempotency_key' => 'event-1', 'order_reference' => 'order-reference-id', 'item_id' => 'item-1',
            'event_type' => 'DELIVERED',
        ]),
        new CardInstallmentsRequest(['bin' => '54066200', 'amount' => 1000, 'currency' => 'TRY', 'is_three_d' => true]),
        new InstallmentTableRequest(['amount' => 1000, 'currency' => 'TRY']),
        new CardTokenizeRequest([
            'card_number' => '4111111111111111', 'holder_name' => 'John Doe',
            'expiry_month' => '12', 'expiry_year' => '30', 'cvv' => '123',
        ]),
        new MarketplaceProfileUpdate(['name' => 'New name', 'revision' => '2026-10-20T10:00:00.123456789Z']),
        new MarketplacePayoutFilter(['status' => 'WAITING_APPROVAL', 'page' => 1]),
        new MarketplacePayoutApproval(['ids' => ['payout-1']]),
        new MarketplaceApprovalPolicyInput([
            'name' => 'Large orders', 'priority' => 1, 'min_amount' => '1000', 'currency_id' => 'TRY', 'mode' => 'manual',
        ]),
    ];

    foreach ($models as $model) {
        expect($model->toArray())->not->toBeEmpty();
    }
});

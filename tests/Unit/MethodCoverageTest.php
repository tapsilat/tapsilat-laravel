<?php

use Tapsilat\Laravel\TapsilatManager;

describe('Order Methods', function () {
    test('manager has createOrder method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'createOrder'))->toBeTrue();
    });

    test('manager has createSimpleOrder method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'createSimpleOrder'))->toBeTrue();
    });

    test('manager has getOrder method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrder'))->toBeTrue();
    });

    test('manager has getOrderByConversationId method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderByConversationId'))->toBeTrue();
    });

    test('manager has getOrderList method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderList'))->toBeTrue();
    });

    test('manager has getOrders method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrders'))->toBeTrue();
    });

    test('manager has getOrderSubmerchants method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderSubmerchants'))->toBeTrue();
    });

    test('manager has getCheckoutUrl method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getCheckoutUrl'))->toBeTrue();
    });

    test('manager has cancelOrder method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'cancelOrder'))->toBeTrue();
    });

    test('manager has refundOrder method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'refundOrder'))->toBeTrue();
    });

    test('manager has refundOrderSimple method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'refundOrderSimple'))->toBeTrue();
    });

    test('manager has refundAllOrder method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'refundAllOrder'))->toBeTrue();
    });

    test('manager has getOrderPaymentDetails method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderPaymentDetails'))->toBeTrue();
    });

    test('manager has getOrderStatus method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderStatus'))->toBeTrue();
    });

    test('manager has getOrderTransactions method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderTransactions'))->toBeTrue();
    });

    test('manager has orderAccounting method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'orderAccounting'))->toBeTrue();
    });

    test('manager has orderPostAuth method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'orderPostAuth'))->toBeTrue();
    });

    test('manager has getSystemOrderStatuses method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemOrderStatuses'))->toBeTrue();
    });
});

describe('Order Term Methods', function () {
    test('manager has getOrderTerm method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderTerm'))->toBeTrue();
    });

    test('manager has createOrderTerm method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'createOrderTerm'))->toBeTrue();
    });

    test('manager has deleteOrderTerm method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'deleteOrderTerm'))->toBeTrue();
    });

    test('manager has updateOrderTerm method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'updateOrderTerm'))->toBeTrue();
    });

    test('manager has refundOrderTerm method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'refundOrderTerm'))->toBeTrue();
    });

    test('manager has orderTerminate method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'orderTerminate'))->toBeTrue();
    });

    test('manager has terminateOrderTerm method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'terminateOrderTerm'))->toBeTrue();
    });

    test('manager has orderManualCallback method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'orderManualCallback'))->toBeTrue();
    });

    test('manager has orderRelatedUpdate method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'orderRelatedUpdate'))->toBeTrue();
    });
});

describe('Subscription Methods', function () {
    test('manager has createSubscription method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'createSubscription'))->toBeTrue();
    });

    test('manager has getSubscription method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSubscription'))->toBeTrue();
    });

    test('manager has getSubscriptionByReferenceId method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSubscriptionByReferenceId'))->toBeTrue();
    });

    test('manager has getSubscriptionByExternalId method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSubscriptionByExternalId'))->toBeTrue();
    });

    test('manager has listSubscriptions method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'listSubscriptions'))->toBeTrue();
    });

    test('manager has cancelSubscription method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'cancelSubscription'))->toBeTrue();
    });

    test('manager has cancelSubscriptionByReferenceId method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'cancelSubscriptionByReferenceId'))->toBeTrue();
    });

    test('manager has cancelSubscriptionByExternalId method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'cancelSubscriptionByExternalId'))->toBeTrue();
    });

    test('manager has redirectSubscription method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'redirectSubscription'))->toBeTrue();
    });
});

describe('Organization & Health Methods', function () {
    test('manager has getOrganizationSettings method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrganizationSettings'))->toBeTrue();
    });

    test('manager has healthCheck method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'healthCheck'))->toBeTrue();
    });

    test('manager has verifyWebhook method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'verifyWebhook'))->toBeTrue();
    });
});

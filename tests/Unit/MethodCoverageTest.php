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

describe('Newly Synced Methods', function () {
    test('manager has setDebug method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'setDebug'))->toBeTrue();
    });

    test('manager has getSystemBasketItemTypes method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemBasketItemTypes'))->toBeTrue();
    });

    test('manager has getSystemErrorCodes method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemErrorCodes'))->toBeTrue();
    });

    test('manager has getSystemPaymentTermStatuses method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemPaymentTermStatuses'))->toBeTrue();
    });

    test('manager has getSystemProductTypes method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemProductTypes'))->toBeTrue();
    });

    test('manager has getSystemShortcutTypes method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemShortcutTypes'))->toBeTrue();
    });

    test('manager has getSystemTransactionPaymentTypes method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemTransactionPaymentTypes'))->toBeTrue();
    });

    test('manager has getSystemTransactionPurposes method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemTransactionPurposes'))->toBeTrue();
    });

    test('manager has getSystemTransactionStatuses method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSystemTransactionStatuses'))->toBeTrue();
    });

    test('manager has relatedUpdate method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'relatedUpdate'))->toBeTrue();
    });

    test('manager has terminateOrder method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'terminateOrder'))->toBeTrue();
    });

    test('manager has manualCallback method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'manualCallback'))->toBeTrue();
    });

    test('manager has getOrderPayments method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderPayments'))->toBeTrue();
    });

    test('manager has getOrderPdf method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderPdf'))->toBeTrue();
    });

    test('manager has getOrderExcel method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderExcel'))->toBeTrue();
    });

    test('manager has createOrderRefundRequest method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'createOrderRefundRequest'))->toBeTrue();
    });

    test('manager has addOrderOip method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'addOrderOip'))->toBeTrue();
    });

    test('manager has getOrderPaymentDetailsById method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrderPaymentDetailsById'))->toBeTrue();
    });

    test('manager has updatePaymentOptions method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'updatePaymentOptions'))->toBeTrue();
    });

    test('manager has splitOrderItemPayment method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'splitOrderItemPayment'))->toBeTrue();
    });

    test('manager has orderCallback method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'orderCallback'))->toBeTrue();
    });

    test('manager has orderVposQuery method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'orderVposQuery'))->toBeTrue();
    });

    test('manager has getOrganizationSuborganizationDetails method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrganizationSuborganizationDetails'))->toBeTrue();
    });

    test('manager has getOrganizationSuborganizationSubmerchants method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrganizationSuborganizationSubmerchants'))->toBeTrue();
    });

    test('manager has getOrganizationCurrencyPresets method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getOrganizationCurrencyPresets'))->toBeTrue();
    });

    test('manager has createOrganizationUserToken method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'createOrganizationUserToken'))->toBeTrue();
    });

    test('manager has createSubmerchant method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'createSubmerchant'))->toBeTrue();
    });

    test('manager has getSubmerchant method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSubmerchant'))->toBeTrue();
    });

    test('manager has getSuborganizationBySubmerchant method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'getSuborganizationBySubmerchant'))->toBeTrue();
    });

    test('manager has updateSubmerchant method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'updateSubmerchant'))->toBeTrue();
    });

    test('manager has deleteSubmerchant method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'deleteSubmerchant'))->toBeTrue();
    });

    test('manager has listSubmerchants method', function () {
        $manager = app(TapsilatManager::class);
        expect(method_exists($manager, 'listSubmerchants'))->toBeTrue();
    });

});

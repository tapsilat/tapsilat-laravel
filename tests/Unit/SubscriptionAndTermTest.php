<?php

use Tapsilat\Laravel\TapsilatManager;
use Tapsilat\TapsilatAPI;
use Tapsilat\Models\SubscriptionCreateRequest;
use Tapsilat\Models\SubscriptionGetRequest;
use Tapsilat\Models\SubscriptionCancelRequest;
use Tapsilat\Models\SubscriptionCreateResponse;
use Tapsilat\Models\SubscriptionDetail;
use Tapsilat\Models\OrderPaymentTermCreateDTO;
use Tapsilat\Models\OrderPaymentTermUpdateDTO;
use Tapsilat\Models\OrderTermRefundRequest;
use Tapsilat\APIException;

beforeEach(function () {
    $this->manager = app(TapsilatManager::class);
    $this->apiMock = Mockery::mock(TapsilatAPI::class);

    $reflection = new ReflectionClass($this->manager);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($this->manager, $this->apiMock);
});

afterEach(function () {
    Mockery::close();
});

describe('Subscription Operations', function () {
    test('createSubscription creates subscription successfully', function () {
        $subscriptionRequest = Mockery::mock(SubscriptionCreateRequest::class);
        $subscriptionRequest->amount = 99.99;
        $subscriptionRequest->title = 'Premium Plan';

        $expectedResponse = [
            'reference_id' => 'sub-ref-123',
            'status' => 'ACTIVE'
        ];

        $mockResponse = Mockery::mock(SubscriptionCreateResponse::class);
        $mockResponse->shouldReceive('getReferenceId')->andReturn('sub-ref-123');

        $this->apiMock
            ->shouldReceive('createSubscription')
            ->once()
            ->with($subscriptionRequest)
            ->andReturn($mockResponse);

        $result = $this->manager->createSubscription($subscriptionRequest);

        expect($result)->toBeInstanceOf(SubscriptionCreateResponse::class);
        expect($result->getReferenceId())->toBe('sub-ref-123');
    });

    test('getSubscription retrieves subscription details', function () {
        $request = new SubscriptionGetRequest('sub-ref-456', null);

        $expectedResponse = [
            'reference_id' => 'sub-ref-456',
            'amount' => 149.99,
            'status' => 'ACTIVE',
            'period' => 30
        ];

        $mockDetail = Mockery::mock(SubscriptionDetail::class);
        $mockDetail->shouldReceive('get')->with('reference_id')->andReturn('sub-ref-456');
        $mockDetail->shouldReceive('get')->with('amount')->andReturn(149.99);
        $mockDetail->shouldReceive('get')->with('status')->andReturn('ACTIVE');

        $this->apiMock
            ->shouldReceive('getSubscription')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof SubscriptionGetRequest;
            }))
            ->andReturn($mockDetail);

        $result = $this->manager->getSubscription($request);

        expect($result)->toBeInstanceOf(SubscriptionDetail::class);
        expect($result->get('reference_id'))->toBe('sub-ref-456');
    });

    test('getSubscriptionByReferenceId uses correct request', function () {
        $referenceId = 'sub-ref-789';

        $mockDetail = Mockery::mock(SubscriptionDetail::class);

        $this->apiMock
            ->shouldReceive('getSubscription')
            ->once()
            ->with(Mockery::on(function ($arg) use ($referenceId) {
                return $arg instanceof SubscriptionGetRequest
                    && $arg->reference_id === $referenceId;
            }))
            ->andReturn($mockDetail);

        $result = $this->manager->getSubscriptionByReferenceId($referenceId);

        expect($result)->toBeInstanceOf(SubscriptionDetail::class);
    });

    test('listSubscriptions returns paginated list', function () {
        $expectedResponse = [
            'page' => 1,
            'per_page' => 10,
            'total' => 25,
            'rows' => [
                ['reference_id' => 'sub-1', 'amount' => 99.99],
                ['reference_id' => 'sub-2', 'amount' => 149.99],
            ]
        ];

        $this->apiMock
            ->shouldReceive('listSubscriptions')
            ->once()
            ->with(1, 10)
            ->andReturn($expectedResponse);

        $result = $this->manager->listSubscriptions(1, 10);

        expect($result)->toBeArray();
        expect($result['total'])->toBe(25);
        expect($result['rows'])->toHaveCount(2);
    });

    test('cancelSubscription cancels subscription successfully', function () {
        $request = new SubscriptionCancelRequest('sub-ref-cancel', null);

        $expectedResponse = [
            'is_success' => true,
            'message' => 'SUBSCRIPTION_CANCELLED'
        ];

        $this->apiMock
            ->shouldReceive('cancelSubscription')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return $arg instanceof SubscriptionCancelRequest;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->cancelSubscription($request);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });

    test('cancelSubscriptionByReferenceId creates correct request', function () {
        $referenceId = 'sub-to-cancel';

        $expectedResponse = ['is_success' => true];

        $this->apiMock
            ->shouldReceive('cancelSubscription')
            ->once()
            ->with(Mockery::on(function ($arg) use ($referenceId) {
                return $arg instanceof SubscriptionCancelRequest
                    && $arg->reference_id === $referenceId;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->cancelSubscriptionByReferenceId($referenceId);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });
});

describe('Order Term Operations', function () {
    test('getOrderTerm retrieves term details', function () {
        $termReferenceId = 'term-ref-123';
        $expectedResponse = [
            'term_reference_id' => $termReferenceId,
            'amount' => 100.00,
            'status' => 'PENDING',
            'due_date' => '2025-12-31'
        ];

        $this->apiMock
            ->shouldReceive('getOrderTerm')
            ->once()
            ->with($termReferenceId)
            ->andReturn($expectedResponse);

        $result = $this->manager->getOrderTerm($termReferenceId);

        expect($result)->toBeArray();
        expect($result['amount'])->toBe(100.00);
        expect($result['status'])->toBe('PENDING');
    });

    test('createOrderTerm creates payment term successfully', function () {
        $termDto = Mockery::mock(OrderPaymentTermCreateDTO::class);
        $termDto->order_id = 'order-123';
        $termDto->amount = 200.00;

        $expectedResponse = [
            'code' => 156050,
            'message' => 'ORDER_ADD_PAYMENT_TERM_SUCCESS'
        ];

        $this->apiMock
            ->shouldReceive('createOrderTerm')
            ->once()
            ->with($termDto)
            ->andReturn($expectedResponse);

        $result = $this->manager->createOrderTerm($termDto);

        expect($result)->toBeArray();
        expect($result['code'])->toBe(156050);
    });

    test('deleteOrderTerm deletes term successfully', function () {
        $orderId = 'order-456';
        $termReferenceId = 'term-to-delete';

        $expectedResponse = [
            'code' => 156090,
            'message' => 'ORDER_REMOVE_PAYMENT_TERM_SUCCESS'
        ];

        $this->apiMock
            ->shouldReceive('deleteOrderTerm')
            ->once()
            ->with(Mockery::on(function ($arg) use ($orderId, $termReferenceId) {
                return $arg instanceof \Tapsilat\Models\OrderPaymentTermDeleteDTO
                    && $arg->order_id === $orderId
                    && $arg->term_reference_id === $termReferenceId;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->deleteOrderTerm($orderId, $termReferenceId);

        expect($result)->toBeArray();
        expect($result['code'])->toBe(156090);
    });

    test('updateOrderTerm updates term successfully', function () {
        $termDto = Mockery::mock(OrderPaymentTermUpdateDTO::class);
        $termDto->term_reference_id = 'term-update';

        $expectedResponse = [
            'is_success' => true,
            'message' => 'TERM_UPDATED'
        ];

        $this->apiMock
            ->shouldReceive('updateOrderTerm')
            ->once()
            ->with($termDto)
            ->andReturn($expectedResponse);

        $result = $this->manager->updateOrderTerm($termDto);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });

    test('refundOrderTerm processes term refund', function () {
        $termRefundRequest = Mockery::mock(OrderTermRefundRequest::class);

        $expectedResponse = [
            'is_success' => true,
            'refund_amount' => 50.00
        ];

        $this->apiMock
            ->shouldReceive('refundOrderTerm')
            ->once()
            ->with($termRefundRequest)
            ->andReturn($expectedResponse);

        $result = $this->manager->refundOrderTerm($termRefundRequest);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });

    test('orderTerminate terminates order', function () {
        $referenceId = 'order-to-terminate';

        $expectedResponse = [
            'is_success' => true,
            'message' => 'ORDER_TERMINATED'
        ];

        $this->apiMock
            ->shouldReceive('orderTerminate')
            ->once()
            ->with(Mockery::on(function ($arg) use ($referenceId) {
                return $arg instanceof \Tapsilat\Models\TerminateRequest
                    && $arg->reference_id === $referenceId;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->orderTerminate($referenceId);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });



    test('orderManualCallback triggers manual callback', function () {
        $referenceId = 'order-callback';
        $conversationId = 'conv-123';

        $expectedResponse = [
            'is_success' => true,
            'message' => 'CALLBACK_TRIGGERED'
        ];

        $this->apiMock
            ->shouldReceive('orderManualCallback')
            ->once()
            ->with(Mockery::on(function ($arg) use ($referenceId, $conversationId) {
                return $arg instanceof \Tapsilat\Models\OrderManualCallbackDTO
                    && $arg->reference_id === $referenceId
                    && $arg->conversation_id === $conversationId;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->orderManualCallback($referenceId, $conversationId);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });

    test('orderRelatedUpdate updates related reference', function () {
        $referenceId = 'order-123';
        $relatedReferenceId = 'related-456';

        $expectedResponse = [
            'is_success' => true,
            'message' => 'RELATED_UPDATED'
        ];

        $this->apiMock
            ->shouldReceive('orderRelatedUpdate')
            ->once()
            ->with(Mockery::on(function ($arg) use ($referenceId, $relatedReferenceId) {
                return $arg instanceof \Tapsilat\Models\OrderRelatedReferenceDTO
                    && $arg->reference_id === $referenceId
                    && $arg->related_reference_id === $relatedReferenceId;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->orderRelatedUpdate($referenceId, $relatedReferenceId);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });
});

describe('Organization & Health', function () {
    test('getOrganizationSettings returns settings', function () {
        $expectedResponse = [
            'organization_name' => 'Test Merchant',
            'currency' => 'TRY',
            'locale' => 'tr'
        ];

        $this->apiMock
            ->shouldReceive('getOrganizationSettings')
            ->once()
            ->andReturn($expectedResponse);

        $result = $this->manager->getOrganizationSettings();

        expect($result)->toBeArray();
        expect($result['organization_name'])->toBe('Test Merchant');
    });

    test('healthCheck returns health status', function () {
        $expectedResponse = [
            'status' => 'UP',
            'timestamp' => '2025-12-18T00:00:00Z'
        ];

        $this->apiMock
            ->shouldReceive('healthCheck')
            ->once()
            ->andReturn($expectedResponse);

        $result = $this->manager->healthCheck();

        expect($result)->toBeArray();
        expect($result['status'])->toBe('UP');
    });
});

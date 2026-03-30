<?php

use Tapsilat\Laravel\TapsilatManager;
use Tapsilat\TapsilatAPI;
use Tapsilat\Models\OrderCreateDTO;
use Tapsilat\Models\BuyerDTO;
use Tapsilat\Models\OrderResponse;
use Tapsilat\Models\RefundOrderDTO;
use Tapsilat\APIException;

beforeEach(function () {
    $this->manager = app(TapsilatManager::class);
    $this->apiMock = Mockery::mock(TapsilatAPI::class);

    // Inject the mock client
    $reflection = new ReflectionClass($this->manager);
    $property = $reflection->getProperty('client');
    $property->setAccessible(true);
    $property->setValue($this->manager, $this->apiMock);
});

afterEach(function () {
    Mockery::close();
});

describe('Order Creation', function () {
    test('createOrder sends correct payload and returns OrderResponse', function () {
        $buyer = new BuyerDTO('John', 'Doe', null, null, null, 'john@example.com');
        $orderDto = new OrderCreateDTO(
            100.00,
            'TRY',
            'tr',
            $buyer
        );

        $expectedResponse = [
            'order_id' => 'order-123',
            'reference_id' => 'ref-456',
            'checkout_url' => 'https://checkout.test.dev?ref=ref-456'
        ];

        $this->apiMock
            ->shouldReceive('createOrder')
            ->once()
            ->with(Mockery::on(function ($arg) use ($orderDto) {
                return $arg instanceof OrderCreateDTO
                    && $arg->amount === $orderDto->amount
                    && $arg->currency === $orderDto->currency;
            }))
            ->andReturn(new OrderResponse($expectedResponse));

        $result = $this->manager->createOrder($orderDto);

        expect($result)->toBeInstanceOf(OrderResponse::class);
        expect($result->getOrderId())->toBe('order-123');
        expect($result->getReferenceId())->toBe('ref-456');
        expect($result->getCheckoutUrl())->toBe('https://checkout.test.dev?ref=ref-456');
    });

    test('createSimpleOrder creates order with minimal parameters', function () {
        $expectedResponse = [
            'order_id' => 'simple-order-123',
            'reference_id' => 'simple-ref-456',
            'checkout_url' => 'https://checkout.test.dev?ref=simple-ref-456'
        ];

        $this->apiMock
            ->shouldReceive('createOrder')
            ->once()
            ->andReturn(new OrderResponse($expectedResponse));

        $result = $this->manager->createSimpleOrder(
            150.00,
            'Jane',
            'Smith',
            'jane@example.com'
        );

        expect($result)->toBeInstanceOf(OrderResponse::class);
        expect($result->getOrderId())->toBe('simple-order-123');
    });
});

describe('Order Retrieval', function () {
    test('getOrder returns order details by reference ID', function () {
        $referenceId = 'ref-789';
        $expectedResponse = [
            'reference_id' => $referenceId,
            'amount' => 200.00,
            'currency' => 'TRY',
            'status' => 'PAID'
        ];

        $this->apiMock
            ->shouldReceive('getOrder')
            ->once()
            ->with($referenceId)
            ->andReturn(new OrderResponse($expectedResponse));

        $result = $this->manager->getOrder($referenceId);

        expect($result)->toBeInstanceOf(OrderResponse::class);
        expect($result->getReferenceId())->toBe($referenceId);
    });

    test('getOrderByConversationId returns order by conversation ID', function () {
        $conversationId = 'conv-123';
        $expectedResponse = [
            'reference_id' => 'ref-from-conv',
            'conversation_id' => $conversationId,
            'status' => 'PENDING'
        ];

        $this->apiMock
            ->shouldReceive('getOrderByConversationId')
            ->once()
            ->with($conversationId)
            ->andReturn(new OrderResponse($expectedResponse));

        $result = $this->manager->getOrderByConversationId($conversationId);

        expect($result)->toBeInstanceOf(OrderResponse::class);
    });

    test('getOrderList returns paginated orders', function () {
        $expectedResponse = [
            'page' => 1,
            'per_page' => 10,
            'total' => 50,
            'rows' => [
                ['reference_id' => 'ref-1', 'amount' => 100],
                ['reference_id' => 'ref-2', 'amount' => 200],
            ]
        ];

        $this->apiMock
            ->shouldReceive('getOrderList')
            ->once()
            ->with(1, 10, '', '', '', '')
            ->andReturn($expectedResponse);

        $result = $this->manager->getOrderList(1, 10);

        expect($result)->toBeArray();
        expect($result['page'])->toBe(1);
        expect($result['total'])->toBe(50);
        expect($result['rows'])->toHaveCount(2);
    });
});

describe('Order Operations', function () {
    test('cancelOrder cancels an order successfully', function () {
        $referenceId = 'ref-to-cancel';
        $expectedResponse = [
            'is_success' => true,
            'message' => 'ORDER_CANCELLED'
        ];

        $this->apiMock
            ->shouldReceive('cancelOrder')
            ->once()
            ->with(Mockery::on(function ($arg) use ($referenceId) {
                return $arg instanceof \Tapsilat\Models\CancelOrderDTO
                    && $arg->reference_id === $referenceId;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->cancelOrder($referenceId);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });

    test('refundOrder processes refund with DTO', function () {
        $refundDto = new RefundOrderDTO(50.00, 'ref-refund', 'item-1');
        $expectedResponse = [
            'is_success' => true,
            'refund_amount' => 50.00
        ];

        $this->apiMock
            ->shouldReceive('refundOrder')
            ->once()
            ->with(Mockery::on(function ($arg) use ($refundDto) {
                return $arg instanceof RefundOrderDTO
                    && $arg->amount === $refundDto->amount;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->refundOrder($refundDto);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
        expect($result['refund_amount'])->toBe(50.00);
    });

    test('refundOrderSimple creates and processes refund', function () {
        $expectedResponse = [
            'is_success' => true,
            'refund_amount' => 75.00
        ];

        $this->apiMock
            ->shouldReceive('refundOrder')
            ->once()
            ->andReturn($expectedResponse);

        $result = $this->manager->refundOrderSimple(75.00, 'ref-simple-refund');

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });

    test('refundAllOrder refunds entire order', function () {
        $referenceId = 'ref-full-refund';
        $expectedResponse = [
            'is_success' => true,
            'message' => 'FULL_REFUND_SUCCESS'
        ];

        $this->apiMock
            ->shouldReceive('refundAllOrder')
            ->once()
            ->with(Mockery::on(function ($arg) use ($referenceId) {
                return $arg instanceof \Tapsilat\Models\RefundAllOrderDTO
                    && $arg->reference_id === $referenceId;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->refundAllOrder($referenceId);

        expect($result)->toBeArray();
        expect($result['is_success'])->toBeTrue();
    });
});

describe('Order Information', function () {
    test('getOrderStatus returns order status', function () {
        $referenceId = 'ref-status';
        $expectedResponse = [
            'status' => 'COMPLETED',
            'status_code' => 8
        ];

        $this->apiMock
            ->shouldReceive('getOrderStatus')
            ->once()
            ->with($referenceId)
            ->andReturn($expectedResponse);

        $result = $this->manager->getOrderStatus($referenceId);

        expect($result)->toBeArray();
        expect($result['status'])->toBe('COMPLETED');
    });

    test('getOrderTransactions returns transaction list', function () {
        $referenceId = 'ref-transactions';
        $expectedResponse = [
            ['id' => 'tx-1', 'amount' => 100, 'type' => 'PAYMENT'],
            ['id' => 'tx-2', 'amount' => 50, 'type' => 'REFUND'],
        ];

        $this->apiMock
            ->shouldReceive('getOrderTransactions')
            ->once()
            ->with($referenceId)
            ->andReturn($expectedResponse);

        $result = $this->manager->getOrderTransactions($referenceId);

        expect($result)->toBeArray();
        expect($result)->toHaveCount(2);
        expect($result[0]['type'])->toBe('PAYMENT');
    });

    test('getOrderPaymentDetails returns payment details', function () {
        $referenceId = 'ref-payment-details';
        $expectedResponse = [
            'card_number' => '****1234',
            'card_type' => 'VISA',
            'installment' => 1
        ];

        $this->apiMock
            ->shouldReceive('getOrderPaymentDetails')
            ->once()
            ->with(Mockery::on(function ($arg) use ($referenceId) {
                return $arg instanceof \Tapsilat\Models\OrderPaymentDetailDTO
                    && $arg->reference_id === $referenceId;
            }))
            ->andReturn($expectedResponse);

        $result = $this->manager->getOrderPaymentDetails($referenceId);

        expect($result)->toBeArray();
        expect($result['card_type'])->toBe('VISA');
    });
});

describe('New Order Methods', function () {
    test('getSystemOrderStatuses returns status list', function () {
        $expectedResponse = [
            ['id' => 1, 'name' => 'CREATED'],
            ['id' => 2, 'name' => 'PENDING'],
            ['id' => 8, 'name' => 'COMPLETED'],
        ];

        $this->apiMock
            ->shouldReceive('getSystemOrderStatuses')
            ->once()
            ->andReturn($expectedResponse);

        $result = $this->manager->getSystemOrderStatuses();

        expect($result)->toBeArray();
        expect($result)->toHaveCount(3);
        expect($result[0]['name'])->toBe('CREATED');
        expect($result[2]['name'])->toBe('COMPLETED');
    });
});

describe('Error Handling', function () {
    test('createOrder throws APIException on error', function () {
        $buyer = new BuyerDTO('Test', 'User', null, null, null, 'test@example.com');
        $orderDto = new OrderCreateDTO(100.00, 'TRY', 'tr', $buyer);

        $this->apiMock
            ->shouldReceive('createOrder')
            ->once()
            ->andThrow(new APIException(400, 100001, 'INVALID_AMOUNT'));

        expect(fn() => $this->manager->createOrder($orderDto))
            ->toThrow(APIException::class);
    });

    test('getOrder throws APIException when order not found', function () {
        $this->apiMock
            ->shouldReceive('getOrder')
            ->once()
            ->with('non-existent-ref')
            ->andThrow(new APIException(404, 101160, 'ORDER_NOT_FOUND'));

        expect(fn() => $this->manager->getOrder('non-existent-ref'))
            ->toThrow(APIException::class);
    });
});

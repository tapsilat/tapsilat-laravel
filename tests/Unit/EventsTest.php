<?php

use Tapsilat\Laravel\Events\OrderPaid;
use Tapsilat\Laravel\Events\OrderFailed;
use Tapsilat\Laravel\Events\OrderRefunded;
use Tapsilat\Laravel\Events\WebhookReceived;
use Tapsilat\Laravel\Tests\TestCase;

uses(TestCase::class);

test('webhook received event contains payload', function () {
    $payload = [
        'event' => 'order.paid',
        'reference_id' => 'test-reference-id',
        'data' => ['amount' => 100],
    ];

    $event = new WebhookReceived($payload);

    expect($event->payload)->toBe($payload);
    expect($event->getEventType())->toBe('order.paid');
    expect($event->getReferenceId())->toBe('test-reference-id');
    expect($event->getData())->toBe(['amount' => 100]);
});

test('order paid event extracts data correctly', function () {
    $payload = [
        'event' => 'order.paid',
        'reference_id' => 'test-reference-id',
        'order_id' => 'test-order-id',
        'amount' => 100.50,
        'currency' => 'TRY',
        'conversation_id' => 'test-conversation-id',
    ];

    $event = new OrderPaid($payload);

    expect($event->getReferenceId())->toBe('test-reference-id');
    expect($event->getOrderId())->toBe('test-order-id');
    expect($event->getAmount())->toBe(100.50);
    expect($event->getCurrency())->toBe('TRY');
    expect($event->getConversationId())->toBe('test-conversation-id');
});

test('order failed event extracts error data', function () {
    $payload = [
        'event' => 'order.failed',
        'reference_id' => 'test-reference-id',
        'order_id' => 'test-order-id',
        'error_code' => 'PAYMENT_DECLINED',
        'error_message' => 'Card declined',
    ];

    $event = new OrderFailed($payload);

    expect($event->getReferenceId())->toBe('test-reference-id');
    expect($event->getOrderId())->toBe('test-order-id');
    expect($event->getErrorCode())->toBe('PAYMENT_DECLINED');
    expect($event->getErrorMessage())->toBe('Card declined');
});

test('order refunded event extracts refund data', function () {
    $payload = [
        'event' => 'order.refunded',
        'reference_id' => 'test-reference-id',
        'order_id' => 'test-order-id',
        'refunded_amount' => 50.25,
        'currency' => 'TRY',
    ];

    $event = new OrderRefunded($payload);

    expect($event->getReferenceId())->toBe('test-reference-id');
    expect($event->getOrderId())->toBe('test-order-id');
    expect($event->getRefundedAmount())->toBe(50.25);
    expect($event->getCurrency())->toBe('TRY');
});

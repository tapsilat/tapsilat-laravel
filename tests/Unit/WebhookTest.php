<?php

use Illuminate\Support\Facades\Event;
use Tapsilat\Laravel\Events\OrderFailed;
use Tapsilat\Laravel\Events\OrderPaid;
use Tapsilat\Laravel\Events\OrderRefunded;
use Tapsilat\Laravel\Events\WebhookReceived;
use Tapsilat\Laravel\TapsilatManager;
use Tapsilat\Models\OrderChargeRequest;
use Tapsilat\TapsilatAPI;

const WEBHOOK_SECRET = 'test-webhook-secret';

/**
 * Builds the Tapsilat-Signature header for a body: t=<unix time>,v1=<hex HMAC-SHA256 of "<t>.<body>"
 * keyed with the webhook secret>.
 */
function tapsilatSignature(string $body, ?int $timestamp = null, string $secret = WEBHOOK_SECRET): string
{
    $timestamp ??= time();

    return 't=' . $timestamp . ',v1=' . hash_hmac('sha256', $timestamp . '.' . $body, $secret);
}

function orderLiteCallback(string $status): string
{
    return json_encode([
        'data' => [
            'id' => '5f0c6f1e-1111-4a5b-9c1d-2e3f4a5b6c7d',
            'orderReferenceId' => '0b6a4f9e-2222-4c3d-8e9f-a1b2c3d4e5f6',
            'conversationId' => 'conv-1001',
            'status' => $status,
        ],
        'type' => 'order_lite',
        'token' => '9d8c7b6a-3333-4e5f-a6b7-c8d9e0f1a2b3',
    ]);
}

function postWebhook($test, string $body, array $headers)
{
    $server = [];
    foreach ($headers as $name => $value) {
        $server['HTTP_' . strtoupper(str_replace('-', '_', $name))] = $value;
    }
    $server['CONTENT_TYPE'] = 'application/json';

    return $test->call('POST', '/tapsilat/webhook', [], [], [], $server, $body);
}

describe('Webhook route', function () {
    beforeEach(function () {
        Event::fake([WebhookReceived::class, OrderPaid::class, OrderFailed::class, OrderRefunded::class]);
    });

    test('a signed order_lite callback for a paid order dispatches OrderPaid', function () {
        $body = orderLiteCallback('3');

        postWebhook($this, $body, ['Tapsilat-Signature' => tapsilatSignature($body)])
            ->assertOk()
            ->assertJson(['success' => true]);

        Event::assertDispatched(WebhookReceived::class, fn ($event) => $event->getEventType() === 'order_lite');
        Event::assertDispatched(OrderPaid::class, function (OrderPaid $event) {
            return $event->getReferenceId() === '0b6a4f9e-2222-4c3d-8e9f-a1b2c3d4e5f6'
                && $event->getConversationId() === 'conv-1001'
                && $event->getOrderId() === '5f0c6f1e-1111-4a5b-9c1d-2e3f4a5b6c7d'
                && $event->getStatus() === 'Paid';
        });
        Event::assertNotDispatched(OrderFailed::class);
    });

    test('a failed order callback dispatches OrderFailed', function () {
        $body = json_encode([
            'data' => ['id' => 'o-1', 'reference_id' => 'ref-1', 'conversation_id' => 'c-1', 'status' => 13],
            'type' => 'order',
            'token' => 't-1',
        ]);

        postWebhook($this, $body, ['Tapsilat-Signature' => tapsilatSignature($body)])->assertOk();

        Event::assertDispatched(OrderFailed::class, fn (OrderFailed $event) => $event->getReferenceId() === 'ref-1');
        Event::assertNotDispatched(OrderPaid::class);
    });

    test('an order_payment_detail callback reads the nested order status', function () {
        $body = json_encode([
            'data' => [
                'id' => 'detail-1',
                'order_reference_id' => 'ref-2',
                'conversation_id' => 'c-2',
                'order' => ['id' => 'o-2', 'reference_id' => 'ref-2', 'status' => 'Paid'],
            ],
            'type' => 'order_payment_detail',
            'token' => 't-2',
        ]);

        postWebhook($this, $body, ['Tapsilat-Signature' => tapsilatSignature($body)])->assertOk();

        Event::assertDispatched(OrderPaid::class, function (OrderPaid $event) {
            return $event->getReferenceId() === 'ref-2' && $event->getOrderId() === 'o-2';
        });
    });

    test('a refunded order callback dispatches OrderRefunded', function () {
        $body = orderLiteCallback('15');

        postWebhook($this, $body, ['Tapsilat-Signature' => tapsilatSignature($body)])->assertOk();

        Event::assertDispatched(OrderRefunded::class);
    });

    test('a callback for an unpaid order dispatches only WebhookReceived', function () {
        $body = orderLiteCallback('7');

        postWebhook($this, $body, ['Tapsilat-Signature' => tapsilatSignature($body)])->assertOk();

        Event::assertDispatched(WebhookReceived::class);
        Event::assertNotDispatched(OrderPaid::class);
        Event::assertNotDispatched(OrderFailed::class);
        Event::assertNotDispatched(OrderRefunded::class);
    });

    test('the X-Tapsilat-Signature header is still read', function () {
        $body = orderLiteCallback('3');

        postWebhook($this, $body, ['X-Tapsilat-Signature' => tapsilatSignature($body)])->assertOk();

        Event::assertDispatched(OrderPaid::class);
    });

    test('a wrong, stale or missing signature is rejected', function () {
        $body = orderLiteCallback('3');

        postWebhook($this, $body, ['Tapsilat-Signature' => tapsilatSignature($body, null, 'other-secret')])
            ->assertStatus(401);
        postWebhook($this, $body, ['Tapsilat-Signature' => tapsilatSignature($body, time() - 301)])
            ->assertStatus(401);
        postWebhook($this, orderLiteCallback('13'), ['Tapsilat-Signature' => tapsilatSignature($body)])
            ->assertStatus(401);
        postWebhook($this, $body, [])->assertStatus(401);

        Event::assertNothingDispatched();
    });

    test('a signed body that is not a JSON object is rejected', function () {
        $body = '"not an object"';

        postWebhook($this, $body, ['Tapsilat-Signature' => tapsilatSignature($body)])->assertStatus(400);

        Event::assertNothingDispatched();
    });
});

describe('Webhook verification', function () {
    test('the manager verifies the Tapsilat-Signature header with the configured secret', function () {
        $manager = app(TapsilatManager::class);
        $body = orderLiteCallback('3');

        expect($manager->verifyWebhook($body, tapsilatSignature($body)))->toBeTrue();
        expect($manager->verifyWebhook($body, tapsilatSignature($body, time() - 600)))->toBeFalse();
        expect($manager->verifyWebhook($body, tapsilatSignature($body, time() - 600), null, 0))->toBeTrue();
    });

    test('the tolerance comes from the config', function () {
        config()->set('tapsilat.webhook_tolerance', 900);
        app()->forgetInstance(TapsilatManager::class);
        $body = orderLiteCallback('3');

        expect(app(TapsilatManager::class)->verifyWebhook($body, tapsilatSignature($body, time() - 600)))->toBeTrue();
    });
});

describe('Client forwarding', function () {
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

    test('SDK methods the manager does not wrap are forwarded to the client', function () {
        $request = new OrderChargeRequest('ref-1', true);
        $this->apiMock->shouldReceive('chargeOrder')->once()->with($request)->andReturn(['isSuccess' => true]);

        expect($this->manager->chargeOrder($request))->toBe(['isSuccess' => true]);
    });

    test('an unknown method throws', function () {
        $this->manager->noSuchMethod();
    })->throws(BadMethodCallException::class);

    test('getCheckoutUrl returns null when the order has no checkout URL', function () {
        $this->apiMock->shouldReceive('getCheckoutUrl')->once()->with('ref-1')->andReturn(null);

        expect($this->manager->getCheckoutUrl('ref-1'))->toBeNull();
    });

    test('manualCallback passes the conversation ID', function () {
        $this->apiMock->shouldReceive('manualCallback')->once()->with('ref-1', 'conv-1')->andReturn(['code' => 0]);

        expect($this->manager->manualCallback('ref-1', 'conv-1'))->toBe(['code' => 0]);
    });

    test('getOrganizationLimitsById is forwarded', function () {
        $this->apiMock->shouldReceive('getOrganizationLimitsById')->once()->with('org-1', [])->andReturn(['rows' => []]);

        expect($this->manager->getOrganizationLimitsById('org-1'))->toBe(['rows' => []]);
    });
});

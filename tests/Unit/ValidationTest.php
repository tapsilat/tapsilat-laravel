<?php

use Tapsilat\Laravel\TapsilatManager;
use Tapsilat\APIException;

describe('Validation Tests', function () {
    test('manager validates configuration on initialization', function () {
        $manager = app(TapsilatManager::class);

        // Should have valid config
        expect($manager->getConfig('api_key'))->toBe('test-api-key');
        expect($manager->getConfig('base_url'))->toBe('https://panel.tapsilat.dev/api/v1');
    });

    test('manager returns proper defaults for missing config values', function () {
        $manager = app(TapsilatManager::class);

        // Test default values
        expect($manager->getConfig('timeout', 30))->toBe(30);
        expect($manager->getConfig('default_currency', 'TRY'))->toBe('TRY');
        expect($manager->getConfig('default_locale', 'tr'))->toBe('tr');
    });

    test('manager handles null config keys gracefully', function () {
        $manager = app(TapsilatManager::class);

        // Getting all config
        $allConfig = $manager->getConfig();
        expect($allConfig)->toBeArray();
        expect($allConfig)->toHaveKey('api_key');
    });
});

describe('Client Initialization', function () {
    test('manager initializes client lazily', function () {
        $manager = app(TapsilatManager::class);

        // Client should not be initialized yet
        $reflection = new ReflectionClass($manager);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);

        expect($property->getValue($manager))->toBeNull();

        // Access client
        $client = $manager->client();

        // Now client should be initialized
        expect($property->getValue($manager))->not->toBeNull();
        expect($client)->toBeInstanceOf(\Tapsilat\TapsilatAPI::class);
    });

    test('manager reuses same client instance', function () {
        $manager = app(TapsilatManager::class);

        $client1 = $manager->client();
        $client2 = $manager->client();

        // Should be the same instance
        expect($client1)->toBe($client2);
    });
});

describe('Error Handling', function () {
    test('manager methods exist and are callable', function () {
        $manager = app(TapsilatManager::class);

        // Test that critical methods are callable
        expect(is_callable([$manager, 'createOrder']))->toBeTrue();
        expect(is_callable([$manager, 'getOrder']))->toBeTrue();
        expect(is_callable([$manager, 'cancelOrder']))->toBeTrue();
        expect(is_callable([$manager, 'refundOrder']))->toBeTrue();
        expect(is_callable([$manager, 'createSubscription']))->toBeTrue();
        expect(is_callable([$manager, 'healthCheck']))->toBeTrue();
    });
});

describe('Webhook Verification', function () {
    test('manager can verify webhook signatures', function () {
        $manager = app(TapsilatManager::class);

        $payload = '{"event":"order.paid","data":{"reference_id":"test-123"}}';
        $secret = 'test-webhook-secret';

        // Generate valid signature
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        // Should verify successfully
        $result = $manager->verifyWebhook($payload, $signature, $secret);
        expect($result)->toBeTrue();
    });

    test('manager rejects invalid webhook signatures', function () {
        $manager = app(TapsilatManager::class);

        $payload = '{"event":"order.paid"}';
        $invalidSignature = 'sha256=invalid-signature';
        $secret = 'test-webhook-secret';

        // Should fail verification
        $result = $manager->verifyWebhook($payload, $invalidSignature, $secret);
        expect($result)->toBeFalse();
    });

    test('manager uses config webhook secret when not provided', function () {
        $manager = app(TapsilatManager::class);

        $payload = '{"event":"order.paid"}';
        $configSecret = 'test-webhook-secret'; // From test config
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $configSecret);

        // Should use config secret
        $result = $manager->verifyWebhook($payload, $signature);
        expect($result)->toBeTrue();
    });
});

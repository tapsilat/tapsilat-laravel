<?php

use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Laravel\TapsilatManager;
use Tapsilat\Laravel\Tests\TestCase;

uses(TestCase::class);

test('facade resolves to manager', function () {
    expect(Tapsilat::getFacadeRoot())->toBeInstanceOf(TapsilatManager::class);
});

test('facade can access config', function () {
    expect(Tapsilat::getConfig('api_key'))->toBe('test-api-key');
});

test('facade can verify webhook signature', function () {
    $payload = 'test-payload';
    $secret = 'test-secret';
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

    expect(Tapsilat::verifyWebhook($payload, $expectedSignature, $secret))->toBeTrue();
    expect(Tapsilat::verifyWebhook($payload, 'invalid-signature', $secret))->toBeFalse();
});

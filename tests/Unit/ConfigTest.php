<?php

test('config is published correctly', function () {
    expect(config('tapsilat.api_key'))->toBe('test-api-key');
    expect(config('tapsilat.webhook_secret'))->toBe('test-webhook-secret');
    expect(config('tapsilat.timeout'))->toBe(30);
    expect(config('tapsilat.default_currency'))->toBe('TRY');
    expect(config('tapsilat.default_locale'))->toBe('tr');
});

test('config has all required keys', function () {
    $requiredKeys = [
        'api_key',
        'base_url',
        'timeout',
        'webhook_secret',
        'default_currency',
        'default_locale',
        'logging',
    ];

    foreach ($requiredKeys as $key) {
        expect(config("tapsilat.{$key}"))->not->toBeNull();
    }

    // These keys should exist but can be null
    $optionalKeys = [
        'payment_success_url',
        'payment_failure_url',
    ];

    foreach ($optionalKeys as $key) {
        expect(config()->has("tapsilat.{$key}"))->toBeTrue();
    }
});

test('logging config has required keys', function () {
    expect(config('tapsilat.logging.enabled'))->toBeBool();
    expect(config('tapsilat.logging.channel'))->toBeString();
});

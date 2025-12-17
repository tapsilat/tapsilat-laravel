<?php

test('config is published correctly', function () {
    expect(config('tapsilat.api_key'))->toBe('test-api-key');
    expect(config('tapsilat.webhook_secret'))->toBe('test-webhook-secret');
    expect(config('tapsilat.timeout'))->toBe(30);
    expect(config('tapsilat.default_currency'))->toBe('TRY');
    expect(config('tapsilat.default_locale'))->toBe('tr');
});

test('config has all required keys', function () {
    $keys = [
        'api_key',
        'base_url',
        'timeout',
        'webhook_secret',
        'default_currency',
        'default_locale',
        'payment_success_url',
        'payment_failure_url',
        'logging',
    ];

    foreach ($keys as $key) {
        expect(config("tapsilat.{$key}"))->not->toBeNull();
    }
});

test('logging config has required keys', function () {
    expect(config('tapsilat.logging.enabled'))->toBeBool();
    expect(config('tapsilat.logging.channel'))->toBeString();
});

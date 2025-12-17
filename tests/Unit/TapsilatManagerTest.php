<?php

use Tapsilat\Laravel\TapsilatManager;
use Tapsilat\TapsilatAPI;

test('manager can be resolved from container', function () {
    $manager = app(TapsilatManager::class);
    expect($manager)->toBeInstanceOf(TapsilatManager::class);
});

test('manager can get api client', function () {
    $manager = app(TapsilatManager::class);
    $client = $manager->client();
    expect($client)->toBeInstanceOf(TapsilatAPI::class);
});

test('manager can get configuration', function () {
    $manager = app(TapsilatManager::class);

    expect($manager->getConfig('api_key'))->toBe('test-api-key');
    expect($manager->getConfig('webhook_secret'))->toBe('test-webhook-secret');
    expect($manager->getConfig('default_currency'))->toBe('TRY');
});

test('manager returns null for missing config', function () {
    $manager = app(TapsilatManager::class);
    expect($manager->getConfig('non_existent_key'))->toBeNull();
});

test('manager returns default for missing config', function () {
    $manager = app(TapsilatManager::class);
    expect($manager->getConfig('non_existent_key', 'default_value'))->toBe('default_value');
});

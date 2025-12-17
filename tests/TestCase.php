<?php

namespace Tapsilat\Laravel\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Tapsilat\Laravel\TapsilatServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            TapsilatServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Tapsilat' => \Tapsilat\Laravel\Facades\Tapsilat::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('tapsilat.api_key', 'test-api-key');
        $app['config']->set('tapsilat.webhook_secret', 'test-webhook-secret');
        $app['config']->set('tapsilat.timeout', 30);
        $app['config']->set('tapsilat.default_currency', 'TRY');
        $app['config']->set('tapsilat.default_locale', 'tr');
    }
}

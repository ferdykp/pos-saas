<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        // Fail before RefreshDatabase can run migrations against a business database.
        if (! $app->environment('testing')
            || $app->configurationIsCached()
            || config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \LogicException('Tests require uncached testing config and an in-memory SQLite database.');
        }

        return $app;
    }
}

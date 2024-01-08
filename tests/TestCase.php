<?php

namespace L5SwaggerExtATC\Tests;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpLaravel();
    }

    protected function setUpLaravel()
    {
        $app = new Container();
        $app->singleton('app', 'Illuminate\Container\Container');
        $app->singleton('files', 'Illuminate\Filesystem\Filesystem');

        Facade::setFacadeApplication($app);
    }
}

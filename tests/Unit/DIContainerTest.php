<?php

use PHPUnit\Framework\TestCase;
use GuiBranco\PocMvc\Src\Container\DIContainer;

class DIContainerTest extends TestCase
{
    private DIContainer $container;

    protected function setUp(): void
    {
        $this->container = new DIContainer();
    }

    public function testAddService(): void
    {
        $this->container->set('config', function () {
            return ['debug' => true];
        });

        $this->assertTrue($this->container->has('config'));
    }

    public function testGetService(): void
    {
        $this->container->set('db', function () {
            return new stdClass();
        });

        $service = $this->container->get('db');
        $this->assertInstanceOf(stdClass::class, $service);
    }

    public function testServiceNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->container->get('nonExistentService');
    }
}

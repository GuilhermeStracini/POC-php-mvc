<?php

use GuiBranco\PocMvc\Src\Container\DIContainer;
use PHPUnit\Framework\TestCase;
use GuiBranco\PocMvc\Src\Router\Router;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router(new DIContainer());
    }

    public function testAddRoute(): void
    {
        $this->router->add('GET', '/home', function () {
            return 'Home';
        });

        $this->assertTrue($this->router->hasRoute('GET', '/home'));
    }

    public function testDispatchRoute(): void
    {
        $this->router->add('GET', '/about', function () {
            return 'About Us';
        });

        $response = $this->router->dispatch('GET', '/about');
        $this->assertEquals('About Us', $response);
    }

    public function testRouteNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->router->dispatch('GET', '/non-existent');
    }

    public function testRouteWithQueryString(): void
    {
        $this->router->add('GET', '/about', function () {
            return 'About Us';
        });

        $response = $this->router->dispatch('GET', '/about?test=12345&qs=qs');
        $this->assertEquals('About Us', $response);
    }
}

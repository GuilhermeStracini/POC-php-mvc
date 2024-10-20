<?php

use GuiBranco\PocMvc\App\Controllers\HomeController;
use GuiBranco\PocMvc\Src\Container\DIContainer;
use GuiBranco\PocMvc\Src\Router\Router;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router(new DIContainer());
        $this->router->add('GET', '/home', [new HomeController(''), 'index']);
    }

    public function testHomeRouteDispatch(): void
    {
        $response = $this->router->dispatch('GET', '/home');
        $this->assertEquals('Welcome to the Home page!', $response);
    }
}

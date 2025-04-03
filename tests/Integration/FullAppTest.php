<?php

use PHPUnit\Framework\TestCase;
use GuiBranco\PocMvc\App\Controllers\HomeController;
use GuiBranco\PocMvc\Src\Container\DIContainer;
use GuiBranco\PocMvc\Src\Router\Router;

class FullAppTest extends TestCase
{
    private DIContainer $container;

    private Router $router;

    protected function setUp(): void
    {
        $this->container = new DIContainer();
        $this->container->set(HomeController::class, function () {
            return new HomeController('');
        });

        $this->router = new Router($this->container);
        $this->router->add('GET', '/home', function () {
            $controller = $this->container->get(HomeController::class);
            return $controller->index();
        });
    }

    public function testHomeRouteWithDI(): void
    {
        $response = $this->router->dispatch('GET', '/home');
        $this->assertEquals('Welcome to the Home page!', $response);
    }
}

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
        $viewsPath = __DIR__ . '/../../app/Views';

        $this->router = new Router(new DIContainer());
        $this->router->add('GET', '/home', [new HomeController($viewsPath), 'index']);
    }

    public function testHomeRouteDispatch(): void
    {
        ob_start();
        $this->router->dispatch('GET', '/home/');
        $response = ob_get_clean();

        $this->assertStringContainsString('Welcome to Our MVC Application', $response);
    }
}

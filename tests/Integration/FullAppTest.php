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
        $viewsPath = __DIR__ . '/../../app/Views';

        $this->container = new DIContainer();
        $this->container->set(HomeController::class, function () use ($viewsPath) {
            return new HomeController($viewsPath);
        });

        $this->router = new Router($this->container);
        $this->router->add('GET', '/home', function () {
            $controller = $this->container->get(HomeController::class);
            return $controller->index();
        });
    }

    public function testHomeRouteWithDI(): void
    {
        ob_start();
        $this->router->dispatch('GET', '/home/');
        $response = ob_get_clean();

        $this->assertStringContainsString('Welcome to Our MVC Application', $response);
    }
}

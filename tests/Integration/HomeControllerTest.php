<?php

use PHPUnit\Framework\TestCase;
use GuiBranco\PocMvc\Src\Router;
use GuiBranco\PocMvc\App\Controllers\HomeController;

class HomeControllerTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->add('GET', '/home', [new HomeController(), 'index']);
    }

    public function testHomeRouteDispatch(): void
    {
        $response = $this->router->dispatch('GET', '/home');
        $this->assertEquals('Welcome to the Home page!', $response);
    }
}

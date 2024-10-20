<?php

namespace GuiBranco\PocMvc\App;

use GuiBranco\PocMvc\App\Controllers\AboutController;
use GuiBranco\PocMvc\App\Controllers\ApiController;
use GuiBranco\PocMvc\App\Controllers\ContactController;
use GuiBranco\PocMvc\App\Controllers\HomeController;
use GuiBranco\PocMvc\App\Controllers\UsersController;
use GuiBranco\PocMvc\Src\Application;
use GuiBranco\PocMvc\Src\Container\DIContainer;
use GuiBranco\PocMvc\Src\Router\Router;

class Registration
{
    protected Router $router;
    protected DIContainer $container;

    public function __construct(Application $app)
    {
        $this->router = $app->getRouter();
        $this->container = $app->getContainer();
    }

    public function addServices(): void
    {
        $viewsPath = __DIR__ . '/views';
        $this->container->set(AboutController::class, fn($c) => new AboutController($viewsPath));
        $this->container->set(ContactController::class, fn($c) => new ContactController($viewsPath));
        $this->container->set(HomeController::class, fn($c) => new HomeController($viewsPath));
        $this->container->set(UsersController::class, fn($c) => new UsersController($viewsPath));

        $this->container->set(ApiController::class, fn($c) => new ApiController());
    }

    public function registerRoutes(): void
    {
        // SSR (HTML)
        $this->router->add('GET', '/', [$this->container->get(HomeController::class), 'index']);
        $this->router->add('GET', '/about', [$this->container->get(AboutController::class), 'index']);
        $this->router->add('GET', '/contact', [$this->container->get(ContactController::class), 'index']);
        $this->router->add('POST', '/submit', [$this->container->get(ContactController::class), 'process']);
        $this->router->add('GET', '/users', [$this->container->get(UsersController::class), 'index']);
        $this->router->add('GET', '/users/{id}', [$this->container->get(UsersController::class), 'show']);

        // API (JSON)
        $this->router->add('GET', '/api/v1', [$this->container->get(id: ApiController::class), 'index']);
    }
}

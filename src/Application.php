<?php

namespace GuiBranco\PocMvc\Src;
use GuiBranco\PocMvc\Src\Container\DIContainer;
use GuiBranco\PocMvc\Src\Router\Router;

class Application
{
    protected Router $router;
    protected DIContainer $container;

    public function __construct()
    {
        $this->container = new DIContainer();
        $this->router = new Router($this->container);
    }

    /**
     * Get the DI container instance.
     *
     * @return DIContainer
     */
    public function getContainer(): DIContainer
    {
        return $this->container;
    }

    /**
     * Expose the method to register services in the DI container.
     *
     * @param string $service
     * @param callable $resolver
     * @return void
     */
    public function register(string $service, callable $resolver): void
    {
        $this->container->set($service, $resolver);
    }

    /**
     * Get the router instance.
     *
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Expose the method to add routes to the router.
     *
     * @param string $method
     * @param string $path
     * @param callable $handler
     * @return void
     */
    public function addRoute(string $method, string $path, callable $handler): void
    {
        $this->router->add($method, $path, $handler);
    }

    /**
     * Run the application.
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $response = $this->router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
            echo $response;
        } catch (\Exception $e) {
            http_response_code(404);
            echo '404 Not Found';
        }
    }
}

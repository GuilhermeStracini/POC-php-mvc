<?php

namespace GuiBranco\PocMvc\Src\Core;

use GuiBranco\PocMvc\Src\Core\HttpException;
use GuiBranco\PocMvc\Src\Container\DIContainer;
use GuiBranco\PocMvc\Src\Router\Router;

class Application
{
    protected Router $router;
    protected DIContainer $container;

    protected string $basePath;

    public function __construct(string $basePath = '', string $publicDirBasePath = 'public/')
    {
        $this->container = new DIContainer();
        $this->router = new Router($this->container, $basePath, $publicDirBasePath);
        $this->basePath = $basePath;
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
     * Get the base path for this instance.
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
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
        } catch (HttpException $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        } catch (\Exception $e) {
            http_response_code(500);
            
            if (getenv('APP_ENV') === 'production') {
                echo '500 Internal Server Error';
            } else {
                echo sprintf(
                    "Error: %s\nFile: %s\nLine: %d\nTrace: %s",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getTraceAsString()
                );
            }
        }
    }
}

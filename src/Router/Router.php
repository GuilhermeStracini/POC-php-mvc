<?php

namespace GuiBranco\PocMvc\Src\Router;

class Router
{
    private $routes = [];

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => rtrim($path, '/'),
            'handler' => $handler,
        ];
    }

    private static function getApiMethodVerbsAndNames()
    {
        return [
            'index' => 'GET',
            'list' => 'GET',
            'show' => 'GET',
            'create' => 'POST',
            'update' => 'PUT',
            'delete' => 'DELETE',
        ];
    }

    public function registerApiController($controller, string $prefix = '/api/v1'): void
    {
        $controllerName = (new \ReflectionClass($controller))->getShortName();
        $basePath = strtolower(str_replace('ApiController', '', $controllerName));

        $methods = (new \ReflectionClass($controller))->getMethods();
        foreach ($methods as $method) {
            if ($method->class === $controller) {
                $httpMethod = strtoupper($method->getName());
                if (in_array($httpMethod, ['GET', 'POST', 'PUT', 'DELETE'])) {
                    $this->routes[] = [
                        'method' => $httpMethod,
                        'path' => "{$prefix}/{$basePath}/" . ($httpMethod === 'GET' ? '{id}' : ''),
                        'handler' => [$this->container->get($controller), $method->getName()],
                    ];
                } elseif (in_array(strtolower($method->getName()), array_keys(self::getApiMethodVerbsAndNames()))) {
                    $this->routes[] = [
                        'method' => self::getApiMethodVerbsAndNames()[strtolower($method->getName())],
                        'path' => "{$prefix}/{$basePath}" . (strtolower($method->getName()) === 'show' ? '/{id}' : ''),
                        'handler' => [$this->container->get($controller), $method->getName()],
                    ];
                }
            }
        }
    }

    /**
     * Dispatch the request to the appropriate route.
     *
     * @param string $method
     * @param string $uri
     * @return mixed
     * @throws Exception
     */
    public function dispatch(string $method, string $uri)
    {
        $method = strtoupper($method);

        $uriWithoutSlash = rtrim($uri, '/');

        if ($method === "GET" && $uri === $uriWithoutSlash) {
            header("Location: $uriWithoutSlash/", true, 301);
            exit();
        }

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route['path']);
            $regex = '#^' . $pattern . '/?$#';

            if ($route['method'] === $method && preg_match($regex, $uriWithoutSlash, $matches)) {
                array_shift($matches);
                $params = [];
                preg_match_all('/\{(\w+)\}/', $route['path'], $paramNames);
                foreach ($paramNames[1] as $index => $paramName) {
                    $params[$paramName] = $matches[$index];
                }
                return call_user_func($route['handler'], $params);
            }
        }

        throw new \Exception("No matching route found for {$method} - {$uri}.");
    }

    /**
     * Check if a route exists.
     *
     * @param string $method
     * @param string $uri
     * @return bool
     */
    public function hasRoute(string $method, string $uri): bool
    {
        $uri = rtrim($uri, '/');

        foreach ($this->routes as $route) {
            if (
                $route['method'] === strtoupper($method) &&
                ($route['path'] === $uri || $route['path'] === rtrim($uri, '/'))
            ) {
                return true;
            }
        }

        return false;
    }
}

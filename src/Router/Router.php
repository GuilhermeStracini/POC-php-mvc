<?php

namespace GuiBranco\PocMvc\Src\Router;

class Router
{
    private $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => rtrim($path, '/'),
            'handler' => $handler,
        ];
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
        $uriWithoutSlash = rtrim($uri, '/');

        if ($uri === $uriWithoutSlash) {
            header("Location: $uriWithoutSlash/", true, 301);
            exit();
        }

        $method = strtoupper($method);

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

        throw new \Exception('No matching route found.');
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

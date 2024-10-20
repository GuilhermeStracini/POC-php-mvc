<?php

namespace GuiBranco\PocMvc\Src\Router;

class Router
{
    private $routes = [];

    private $container;

    protected $baseDir;

    public function __construct($container, $baseDir = 'public/')
    {
        $this->container = $container;
        $this->baseDir = $baseDir;
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
        $id = '/{id}';
        return [
            'GETALL' => ['verb' => 'GET', 'parameter' => ''],
            'GET' => ['verb' => 'GET', 'parameter' => $id],
            'POST' => ['verb' => 'POST', 'parameter' => ''],
            'PUT' => ['verb' => 'PUT', 'parameter' => $id],
            'DELETE' => ['verb' => 'DELETE', 'parameter' => $id],
            'INDEX' => ['verb' => 'GET', 'parameter' => ''],
            'LIST' => ['verb' => 'GET', 'parameter' => ''],
            'SHOW' => ['verb' => 'GET', 'parameter' => $id],
            'CREATE' => ['verb' => 'POST', 'parameter' => ''],
            'UPDATE' => ['verb' => 'PUT', 'parameter' => $id],
        ];
    }

    public function registerApiController($controller, string $prefix = '/api/v1'): void
    {
        $controllerName = (new \ReflectionClass($controller))->getShortName();
        $basePath = strtolower(str_replace('ApiController', '', $controllerName));

        $methods = (new \ReflectionClass($controller))->getMethods();
        $mapping = self::getApiMethodVerbsAndNames();

        foreach ($methods as $method) {
            if ($method->class !== $controller) {
                continue;
            }
            $classMethod = strtoupper($method->getName());

            if (!array_key_exists($classMethod, $mapping)) {
                continue;
            }

            $details = $mapping[$classMethod];
            $verb = $details['verb'];
            $path = "{$prefix}/{$basePath}" . ($details['parameter'] ? $details['parameter'] : "");

            $this->routes[] = [
                'method' => $verb,
                'path' => $path,
                'handler' => [$this->container->get($controller), $method->getName()],
            ];
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
        if ($this->isStaticFile($uri)) {
            return $this->serveStaticFile($uri);
        }
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

    protected function isStaticFile($uri)
    {
        $filePath = $this->baseDir . $uri;

        if (file_exists($filePath) && is_file($filePath)) {
            return true;
        }

        return false;
    }

    protected function serveStaticFile($filePath)
    {
        $mimeType = mime_content_type($filePath);
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}

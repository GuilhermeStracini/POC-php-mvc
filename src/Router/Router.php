<?php

namespace GuiBranco\PocMvc\Src\Router;

use GuiBranco\PocMvc\Src\Core\HttpException;

class Router
{
    private $routes = [];
    private $container;
    protected $basePath;
    protected $publicDirBasePath;

    public function __construct($container, string $basePath = '', string $publicDirBasePath = 'public/')
    {
        $this->container = $container;
        $this->basePath = rtrim($basePath, '/');
        $this->publicDirBasePath = rtrim($publicDirBasePath, '/');
    }

    /**
     * Return a list of pre-defined method names for a RESTful API controller.
     * @return string[][]
     */
    private static function getApiMethodVerbsAndNames(): array
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

    /**
     * Add a new route.
     *
     * @param string $method
     * @param string $path
     * @param callable $handler
     * @return void
     */
    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    /**
     * Register a controller as an API controller.
     * @param mixed $controller
     * @param string $prefix
     * @return void
     */
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
                'path' => rtrim($this->basePath . '/' . ltrim($path, '/'), '/'),
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
    public function dispatch(string $method, string $uri): mixed
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        if (!empty($this->basePath) && strpos($uri, $this->basePath) === 0) {
            $basePathLength = strlen($this->basePath);
            if (strlen($uri) === $basePathLength || $uri[$basePathLength] === '/') {
                $uri = substr($uri, $basePathLength);
            }
        }

        if ($this->isStaticFile($uri)) {
            $this->serveStaticFile($uri);
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

            if ($route['method'] === $method && preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                $params = [];
                preg_match_all('/\{(\w+)\}/', $route['path'], $paramNames);
                foreach ($paramNames[1] as $index => $paramName) {
                    $params[$paramName] = $matches[$index];
                }
                return call_user_func($route['handler'], $params);
            }
        }

        throw new HttpException("No matching route found for {$method} - {$uri}.", 404);
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
        if (!empty($this->basePath) && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

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

    /**
     * Check if the requested URI is a static file.
     *
     * @param string $uri
     * @return bool
     */
    protected function isStaticFile($uri): bool
    {
        $filePath = $this->publicDirBasePath . '/' . ltrim($uri, '/');

        return file_exists($filePath) && is_file($filePath);
    }

    /**
     * Serve a static file.
     *
     * @param string $filePath
     * @return void
     */
    protected function serveStaticFile($filePath): never
    {
        $fullPath = $this->publicDirBasePath . '/' . ltrim($filePath, '/');

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            http_response_code(404);
            exit('File not found');
        }

        $mimeType = mime_content_type($fullPath);
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }
}

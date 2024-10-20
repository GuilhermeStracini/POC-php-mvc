<?php

namespace GuiBranco\PocMvc\Src\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;

class DIContainer implements ContainerInterface
{
    protected array $services = [];

    /**
     * Register a service with a closure.
     *
     * @param string $id
     * @param callable $service
     */
    public function set(string $id, callable $service): void
    {
        $this->services[$id] = $service;
    }

    /**
     * Get a service instance.
     *
     * @param string $id
     * @return mixed
     * @throws \Exception
     */
    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \Exception("Service not found: $id");
        }

        if (is_callable($this->services[$id])) {
            return $this->services[$id]($this);
        }

        return $this->resolve($this->services[$id]);
    }

    /**
     * Resolve a class with its dependencies.
     *
     * @param string $class
     * @return object
     * @throws \ReflectionException
     */
    protected function resolve(string $class)
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class $class is not instantiable.");
        }

        $constructor = $reflection->getConstructor();
        if (is_null($constructor)) {
            return new $class;
        }

        $params = [];
        foreach ($constructor->getParameters() as $parameter) {
            $paramClass = $parameter->getType() ? $parameter->getType()->getName() : null;
            if ($paramClass) {
                $params[] = $this->get($paramClass);
            }
        }

        return $reflection->newInstanceArgs($params);
    }

    /**
     * Check if the container can return an entry for the given identifier.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}

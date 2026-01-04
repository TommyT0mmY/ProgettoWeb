<?php
declare(strict_types=1);

namespace Unibostu\Core;

/**
 * Simple dependency injection container
 */
class Container {
    private array $services = [];
    private array $instances = [];
    
    /**
     * Register a service with a factory callable
     *
     * @param string $name The name of the service
     * @param callable $factory A callable that returns an instance of the service,
     *        it can accept the container as a parameter for nested dependencies.
     */
    public function register(string $name, callable $factory): void {
        $this->services[$name] = $factory;
    }

    /**
     * Get a service instance by name
     *
     * @param string $name The name of the service
     * @return mixed The service instance
     */ 
    public function get(string $name) {
        if (!isset($this->instances[$name])) {
            if (!isset($this->services[$name])) {
                throw new \InvalidArgumentException("Service '$name' not found");
            }
            $this->instances[$name] = call_user_func($this->services[$name], $this);
        }
        return $this->instances[$name];
    }
    

    /**
     * Check if a service is registered
     *
     * @param string $name The name of the service
     * @return bool True if the service is registered, false otherwise
     */
    public function has(string $name): bool {
        return isset($this->services[$name]);
    }
}

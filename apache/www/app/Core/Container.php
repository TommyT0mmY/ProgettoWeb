<?php
declare(strict_types=1);

namespace Unibostu\Core;

/**
 * Simple dependency injection container.
 */
class Container {
    private array $services = [];
    private array $instances = [];
    
    /**
     * Registers a service factory.
     *
     * @param string $name Service identifier (typically class name).
     * @param callable $factory This function has the signature function(Container $container): mixed,
     * the return value is the service instance.
     */
    public function register(string $name, callable $factory): void {
        $this->services[$name] = $factory;
    }

    /**
     * Gets or creates a service instance.
     *
     * Instances are cached after first creation.
     *
     * @param string $name Service identifier.
     * @return mixed Service instance.
     * @throws \InvalidArgumentException If service not registered.
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
     * Checks if a service is registered.
     *
     * @param string $name Service identifier.
     * @return bool True if the service is registered, false otherwise.
     */
    public function has(string $name): bool {
        return isset($this->services[$name]);
    }
}

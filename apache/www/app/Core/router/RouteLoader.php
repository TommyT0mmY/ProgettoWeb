<?php
declare(strict_types=1);

namespace Unibostu\Core\router;

use ReflectionAttribute;
use ReflectionClass;
use Unibostu\Core\router\routes\Route;

class RouteLoader {
    public function __construct(
        private Router $router
    ) {}

    /**
     * Loads routes from the given controller classes.
     * expects class-strings of BaseControllers
     *
     * @param class-string<\Unibostu\Controller\BaseController> ...$controllerClasses One or more controller class names.
     */
    public function load(string ...$controllerClasses): void {
        foreach ($controllerClasses as $controllerClass) {
            $this->loadController($controllerClass);
        }
    }

    /**
     * Loads routes from a single controller class.
     *
     * @param class-string<\Unibostu\Controller\BaseController> $controllerClass The controller class name.
     */
    private function loadController(string $controllerClass): void {
        $reflection = new ReflectionClass($controllerClass);
        foreach ($reflection->getMethods() as $method) {
            foreach($method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                /** @var Route $route */
                $route = $attribute->newInstance();
                $httpMethod = $route->method;
                $httpPath = $route->path;
                $this->router->add($httpMethod, $httpPath, $controllerClass, $method->getName());
            }
        }
    }
}

<?php
declare(strict_types=1);

namespace Unibostu\Core;

use Unibostu\Controller as Ctrl;
use Unibostu\Core\router\Router;
use Unibostu\Core\router\RouteLoader;

class App {
    private Router $router;
    private Container $container;

    public function __construct() {
        $this->router = new Router();
        $this->container = new Container();
        $this->registerServices();
        $this->registerRoutes();
    }

    private function registerServices(): void {
        $this->container->register(RenderingEngine::class, function() {
            return new RenderingEngine();
        });
        $this->container->register(SessionManager::class, function() {
            return new SessionManager();
        });
        $this->container->register(CsrfProtection::class, function(Container $container) {
            return new CsrfProtection($container->get(SessionManager::class));
        });
    }

    private function registerRoutes(): void {
        $routeLoader = new RouteLoader($this->router);
        $routeLoader->load(
            Ctrl\PostController::class,
            Ctrl\HomeController::class,
            Ctrl\AuthController::class,
            Ctrl\CommunityController::class
        );
        return;
    }

    public function run(): void {
        try {
            $request = new Http\Request();
            $response = $this->router->dispatch($request, $this->container);
            $response->send();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    public function handleError(\Exception $e): void {
        http_response_code(500);
        echo "An error occurred: " . htmlspecialchars($e->getMessage()); 
    }
}

<?php
declare(strict_types=1);

namespace Unibostu\Core;

use Unibostu\Controller as Ctrl;
use Unibostu\Core\Http\Request;
use Unibostu\Core\router\Router;
use Unibostu\Core\router\RouteLoader;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\CsrfProtection;

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
        $this->container->register(SessionManager::class, function() {
            return new SessionManager();
        });
        $this->container->register(CsrfProtection::class, function(Container $container) {
            return new CsrfProtection($container->get(SessionManager::class));
        });
        $this->container->register(RenderingEngine::class, function(Container $container) {
            return new RenderingEngine($container);
        });
        $this->container->register(Auth::class, function(Container $container) {
            return new Auth($container->get(SessionManager::class));
        });
    }

    private function registerRoutes(): void {
        $routeLoader = new RouteLoader($this->router);
        $routeLoader->load(
            Ctrl\PostController::class,
            Ctrl\HomeController::class,
            Ctrl\AuthController::class,
            Ctrl\CommunityController::class,
            Ctrl\UserProfileController::class,
            Ctrl\DebugController::class,
            Ctrl\CommentController::class,
            Ctrl\DashboardController::class
        );
        return;
    }

    public function run(): void {
        $request = null;
        try {
            $request = new Request();
            $response = $this->router->dispatch($request, $this->container);
            $response->send();
        } catch (\Exception $e) {
            $this->handleError($e, $request);
        }
    }

    public function handleError(\Exception $e, ?Request $request): void {
        $code = intval($e->getCode() ?: 500);
        if ($request === null) {
            http_response_code(500);
            echo "Internal Server Error";
            exit();
        }
        $isApiRequest = str_starts_with($request->getUri(), '/api/');
        // If unauthorized, user is not authenticated and the URI is not for an API endpoint, redirect to login
        if ($code === 401 && !$isApiRequest) {
            header('Location: /login');
            exit();
        }
        // For API requests, return JSON error response
        if ($isApiRequest) {
            http_response_code($code);
            header('Content-Type: application/json');
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage(),
                "code" => $code
            ]);
            exit();
        }
        http_response_code(intval($code));
        echo "An error occurred: " . htmlspecialchars($e->getMessage()); 
    }
}

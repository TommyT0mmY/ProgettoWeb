<?php
declare(strict_types=1);

namespace Unibostu\Core;

use Unibostu\Controller as Ctrl;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;
use Unibostu\Core\router\Router;
use Unibostu\Core\router\RouteLoader;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\CsrfProtection;

/**
 * Application entry point.
 * Bootstraps services, routes, and dispatches HTTP requests.
 */
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
            Ctrl\CourseController::class,
            Ctrl\UserProfileController::class,
            Ctrl\DebugController::class,
            Ctrl\CommentController::class,
            Ctrl\DashboardController::class,
            Ctrl\CategoryController::class,
            Ctrl\FacultyController::class,
            Ctrl\TagController::class
        );
        return;
    }

    /**
     * Dispatches the current HTTP request and sends the response.
     */
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
            Response::create()
                ->withStatusCode(500)
                ->withHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->withContent("Internal Server Error")
                ->send();
            exit();
        }
        $isApiRequest = str_starts_with($request->getUri(), '/api/');
        // If unauthorized, user is not authenticated and the URI is not for an API endpoint, redirect to login
        if ($code === 401 && !$isApiRequest) {
            Response::create()->redirect('/login')->send();
            exit();
        }
        // For API requests, return JSON error response
        if ($isApiRequest) {
            Response::create()
                ->withStatusCode($code)
                ->json([
                    "success" => false,
                    "error" => $e->getMessage()
                ])
                ->send();
            exit();
        }
        // For non-API requests, return plain text error
        Response::create()
            ->withStatusCode($code)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withContent("An error occurred: " . h($e->getMessage()))
            ->send();
    }
}

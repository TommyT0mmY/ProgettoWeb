<?php
declare(strict_types=1);

namespace Unibostu\Core;

use Unibostu\Controller as Ctrl;

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
        $this->router->get('/', Ctrl\HomeController::class, 'index');

        //Post controller
        $this->router->post('/api/posts/create', Ctrl\PostController::class, 'createPost');
        $this->router->post('/api/posts/search', Ctrl\PostController::class, 'searchPost');
        $this->router->get('/api/posts/:postid/comments', Ctrl\PostController::class, 'showComments');
        $this->router->post('/api/posts/:postid/comments', Ctrl\PostController::class, 'addComment');
        $this->router->delete('/api/posts/:postid', Ctrl\PostController::class, 'deletePost');
        $this->router->delete('/api/posts/:postid/comments/:commentid', Ctrl\PostController::class, 'deleteComment');
        $this->router->post('/api/posts/:postid/like', Ctrl\PostController::class, 'likePost');
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

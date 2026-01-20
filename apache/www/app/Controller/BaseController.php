<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\RenderingEngine; 
use Unibostu\Core\Container;
use Unibostu\Core\Http\Response;
use Unibostu\Core\security\Auth;

abstract class BaseController {
    private RenderingEngine $renderingEngine;
    private Auth $auth;

    public function __construct(Container $container) {
        $this->renderingEngine = $container->get(RenderingEngine::class);
        $this->auth = $container->get(Auth::class);
    }

    protected function render(string $viewName, array $data = []): Response {
        $content = $this->renderingEngine->render($viewName, $data);
        return new Response($content); 
    }

    public function getAuth(): Auth {
        return $this->auth;
    }
}

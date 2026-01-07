<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\RenderingEngine; 
use Unibostu\Core\Container;
use Unibostu\Core\Http\Response;

abstract class BaseController {
    protected Container $container;
    protected RenderingEngine $renderingEngine;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->renderingEngine = $container->get(RenderingEngine::class);
    }

    protected function render(string $viewName, array $data = []): Response {
        $content = $this->renderingEngine->render($viewName, $data);
        return new Response($content); 
    }

    public function getContainer(): Container {
        return $this->container;
    }
}

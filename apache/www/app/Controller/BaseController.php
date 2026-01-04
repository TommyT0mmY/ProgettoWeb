<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\RenderingEngine; 
use Unibostu\Core\Container;
use Unibostu\Core\Http\Response;

abstract class BaseController {
    protected Container $container;
    protected RenderingEngine $view;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->view = $container->get(RenderingEngine::class);
    }

    protected function render(string $template, array $data = []): Response {
        $content = $this->view->render($template, $data);
        return new Response($content); 
    }
}

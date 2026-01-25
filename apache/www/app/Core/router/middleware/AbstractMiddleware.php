<?php
declare(strict_types=1);

namespace Unibostu\Core\router\middleware;

use Unibostu\Core\Container;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestHandlerInterface;
use Unibostu\Core\Http\Response;

/**
 * Abstract base class for middleware components.
 */
abstract class AbstractMiddleware {
    /**
     * The dependency injection container.
     */
    protected ?Container $container = null;

    /**
     * Set the container before calling `process`.
     *
     * @param Container $container The dependency injection container
     */
    public function setContainer(Container $container): void {
        $this->container = $container;
    }

    /**
     * Process an incoming request.
     *
     * @param Request $request The incoming HTTP request
     * @param RequestHandlerInterface $handler The request handler to delegate to
     * @return Response The HTTP response
     */
    abstract public function process(Request $request, RequestHandlerInterface $handler): Response; 
}

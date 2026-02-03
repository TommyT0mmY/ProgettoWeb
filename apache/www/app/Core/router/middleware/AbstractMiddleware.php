<?php
declare(strict_types=1);

namespace Unibostu\Core\router\middleware;

use Unibostu\Core\Container;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestHandlerInterface;
use Unibostu\Core\Http\Response;

/**
 * Abstract base class for middleware components.
 *
 * The container needs to be injected via setContainer() before process() is called.
 */
abstract class AbstractMiddleware {
    protected ?Container $container = null;

    /**
     * Injects the DI container. Call this before process().
     *
     * @param Container $container DI container.
     */
    public function setContainer(Container $container): void {
        $this->container = $container;
    }

    /**
     * Processes the request, optionally delegating to the next handler.
     *
     * @param Request $request Incoming request.
     * @param RequestHandlerInterface $handler Next handler in chain.
     * @return Response HTTP response.
     */
    abstract public function process(Request $request, RequestHandlerInterface $handler): Response; 
}

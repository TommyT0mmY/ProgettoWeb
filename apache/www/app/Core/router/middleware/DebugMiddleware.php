<?php
declare(strict_types=1);

namespace Unibostu\Core\router\middleware;

use Attribute;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestHandlerInterface;
use Unibostu\Core\Http\Response;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class DebugMiddleware extends AbstractMiddleware {
    public function __construct() {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        return $handler->handle($request);
    }
}

<?php
declare(strict_types=1);

namespace Unibostu\Core\router;

use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestHandlerInterface;
use Unibostu\Core\Http\Response;

interface MiddlewareInterface {
    public function process(Request $request, RequestHandlerInterface $handler): Response;
}

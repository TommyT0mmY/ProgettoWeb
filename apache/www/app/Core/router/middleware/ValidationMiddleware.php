<?php
declare(strict_types=1);

namespace Unibostu\Core\router\middleware;

use Attribute;
use Unibostu\Core\exceptions\DomainErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestHandlerInterface;
use Unibostu\Core\Http\Response;
use Unibostu\Core\security\CsrfProtection;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ValidationMiddleware extends AbstractMiddleware {

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        // Csrf protection
        $csrfProtection = $this->container->get(CsrfProtection::class);
        if (!$csrfProtection->validateRequest($request)) {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::GENERIC_ERROR->name]
            ]);
        }
        // Catching validation exceptions
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            return Response::create()->json([
                "success" => false,
                "errors" => $e->getErrorCodes()
            ]);
        }
    }
}


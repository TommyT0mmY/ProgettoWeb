<?php
declare(strict_types=1);

namespace Unibostu\Core\router\middleware;

use Attribute;
use Unibostu\Core\exceptions\DomainErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\exceptions\ValidationExceptionBuilder;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\Http\RequestHandlerInterface;
use Unibostu\Core\Http\Response;
use Unibostu\Core\security\CsrfProtection;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ValidationMiddleware extends AbstractMiddleware {

    public function __construct(
        /** @var array<string, ValidationErrorCode> */
        private array $mandatoryBodyFields = [],
        /** @var string[] */
        private array $optionalBodyFields = [],
        private bool $validateCsrf = true
    ) {}

    public function process(Request $request, RequestHandlerInterface $handler): Response {
        // Csrf protection
        $csrfProtection = $this->container->get(CsrfProtection::class);
        if ($this->validateCsrf && !$csrfProtection->validateRequest($request)) {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::GENERIC_ERROR->name]
            ], 403);
        }
        // Catching validation exceptions
        try {
            // Capturing mandatory and optional body fields
            /** 
             * @var array<string, mixed> $capturedFields 
             * @var ValidationExceptionBuilder $validationExceptionBuilder 
             */
            $capturedFields = [];
            $validExcBuilder = ValidationException::build();
            foreach ($this->mandatoryBodyFields as $fieldName => $errorCode) {
                $fieldValue = $request->post($fieldName);
                // Check for null or empty string (empty string can come from multipart/form-data)
                if ($fieldValue === null || $fieldValue === '') {
                    $validExcBuilder->addError($errorCode);
                }
                $capturedFields[$fieldName] = $fieldValue;
            }
            foreach ($this->optionalBodyFields as $fieldName) {
                $capturedFields[$fieldName] = $request->post($fieldName);
            }
            $validExcBuilder->throwIfAny();
            $request = $request->withAttribute(RequestAttribute::FIELDS, $capturedFields);
            return $handler->handle($request);
        } catch (ValidationException $e) {
            return Response::create()->json([
                "success" => false,
                "errors" => $e->getErrorCodes()
            ], 400);
        }
    }
}


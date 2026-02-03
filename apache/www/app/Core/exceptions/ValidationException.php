<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

/**
 * Exception for input validation errors (HTTP 422).
 * To build this exception, use ValidationException::build().
 */
final class ValidationException extends DomainException {
    protected int $httpStatusCode = 422;
    private array $errors = [];

    public function __construct(
        \UnitEnum $mainErrorCode = ValidationErrorCode::INVALID_REQUEST,
        array $errors = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($mainErrorCode, $code, $previous);
        $this->errors = $errors; 
    }

    /**
     * Returns a list of error code names included in this exception.
     *
     * @return string[] Error code names.
     */
    public function getErrorCodes(): array {
        return array_map(fn($error) => $error->name, $this->errors);
    }

    /**
     * Creates a new builder for ValidationException.
     *
     * @return ValidationExceptionBuilder New builder instance.
     */
    public static function build(): ValidationExceptionBuilder {
        return new ValidationExceptionBuilder();
    }
}

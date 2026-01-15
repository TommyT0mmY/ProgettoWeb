<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

/**
 * Class ValidationException
 *
 * Represents a domain exception for user input validation errors.
 */
final class ValidationException extends DomainException {
    protected int $httpStatusCode = 422;
    private array $errors = [];

    public function __construct(
        \BackedEnum $mainErrorCode,
        array $errors = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($mainErrorCode, $code, $previous);
        $this->errors = $errors; 
    }

    /**
     * Retrieves the list of error codes. 
     *
     * @return array<string> An array of error code names.
     */
    public function getErrorCodes(): array {
        return array_map(fn($error) => $error->name, $this->errors);
    }

    /**
     * Creates a new instance of ValidationExceptionBuilder.
     *
     * @return ValidationExceptionBuilder A new instance of the builder.
     */
    public static function build(): ValidationExceptionBuilder {
        return new ValidationExceptionBuilder();
    }
}

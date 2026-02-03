<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

/**
 * Base exception for domain/business logic errors (HTTP 400).
 */
abstract class DomainException extends \RuntimeException {
    protected int $httpStatusCode = 400;

    public function __construct(
        protected \UnitEnum $enumErrorCode,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($this->enumErrorCode->name, $code, $previous);
    }

    public function getErrorName(): string {
        return $this->enumErrorCode->name;
    }

    public function getHttpStatusCode(): int {
        return $this->httpStatusCode;
    }
}

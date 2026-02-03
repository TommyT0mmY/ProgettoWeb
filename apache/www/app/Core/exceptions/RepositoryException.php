<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

/**
 * Class RepositoryException
 *
 * Represents a repository/data access layer exception.
 * Used for database errors and data persistence failures.
 */
final class RepositoryException extends DomainException {
    protected int $httpStatusCode = 500;

    public function __construct(
        string $message = "Repository operation failed",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            DomainErrorCode::DATABASE_ERROR,
            $code,
            $previous
        );
        // Override the message from parent
        $this->message = $message;
    }
}

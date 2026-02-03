<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Core\Database;
use PDO;
use Unibostu\Core\exceptions\RepositoryException;

/**
 * Base repository with PDO connection and transaction helpers.
 */
abstract class BaseRepository {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Converts a database row to a DTO object.
     *
     * @param array $row Database row.
     * @return object DTO instance.
     */
    abstract protected function rowToDTO(array $row): object;

    protected function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }

    protected function commit(): void {
        $this->pdo->commit();
    }

    protected function rollback(): void {
        $this->pdo->rollBack();
    }

    /**
     * @return bool True if in transaction.
     */
    protected function inTransaction(): bool {
        return $this->pdo->inTransaction();
    }

    /**
     * Executes a callback within a database transaction.
     *
     * Automatically commits on success or rolls back on failure.
     *
     * @param callable $callback Operation to execute.
     * @return mixed Callback return value.
     * @throws RepositoryException
     */
    protected function executeInTransaction(callable $callback): mixed {
        $this->beginTransaction();
        try {
            $result = $callback();
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollback();
            }
            // Wrap in RepositoryException if not already one
            if (!($e instanceof RepositoryException)) {
                throw new RepositoryException(
                    $e->getMessage(),
                    (int)$e->getCode(),
                    $e
                );
            }
            throw $e;
        }
    }
}

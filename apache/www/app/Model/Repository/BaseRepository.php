<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Core\Database;
use PDO;

/**
 * Abstract BaseRepository
 *
 * Provides common functionality for all repositories including:
 * - PDO connection management
 * - Transaction handling helpers
 * - Common query patterns
 */
abstract class BaseRepository {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Convert a database row array to a DTO object
     * Subclasses must implement this method
     *
     * @param array $row Database row as associative array
     * @return object The corresponding DTO object
     */
    abstract protected function rowToDTO(array $row): object;

    /**
     * Begin a database transaction
     */
    protected function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }

    /**
     * Commit the current transaction
     */
    protected function commit(): void {
        $this->pdo->commit();
    }

    /**
     * Rollback the current transaction
     */
    protected function rollback(): void {
        $this->pdo->rollBack();
    }

    /**
     * Check if currently in a transaction
     */
    protected function inTransaction(): bool {
        return $this->pdo->inTransaction();
    }
}

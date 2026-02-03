<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\CategoryDTO;
use Unibostu\Core\exceptions\RepositoryException;
use PDO;

class CategoryRepository extends BaseRepository {
    /**
     * Retrieves a category by ID
     * 
     * @param int $categoryId The ID of the category
     * @return CategoryDTO|null The CategoryDTO object or null if not found
     */
    public function findById(int $categoryId): ?CategoryDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categories WHERE category_id = :categoryId"
        );
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Retrieves a category by name
     * 
     * @param string $categoryName The name of the category
     * @return CategoryDTO|null The CategoryDTO object or null if not found
     */
    public function findByName(string $categoryName): ?CategoryDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categories WHERE category_name = :categoryName"
        );
        $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Retrieves all categories
     */
    public function findAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categories ORDER BY category_id"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Search categories by name
     * 
     * @param string $searchTerm The search term
     */
    public function searchByName(string $searchTerm): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categories WHERE category_name LIKE :searchTerm ORDER BY category_id"
        );
        $likeTerm = '%' . $searchTerm . '%';
        $stmt->bindValue(':searchTerm', $likeTerm, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Saves a new category
     * @throws RepositoryException in case of error
     */
    public function save(string $categoryName): void {
        try {
            $this->beginTransaction();
            $stmt = $this->pdo->prepare(
                "INSERT INTO categories (category_name)
                 VALUES (:categoryName)"
            );
            $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to save category record");
            }
            $this->commit();
        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollback();
            }
            throw new RepositoryException("Failed to save category: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Updates a category
     * 
     * @param int $categoryId The ID of the category to update
     * @param string $categoryName The new name of the category
     * @throws RepositoryException in case of error
     */
    public function update(int $categoryId, string $categoryName): void {
        try {
            $this->beginTransaction();
            $stmt = $this->pdo->prepare(
                "UPDATE categories SET category_name = :categoryName WHERE category_id = :categoryId"
            );
            $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
            $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to update category record");
            }
            $this->commit();
        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollback();
            }
            throw new RepositoryException("Failed to update category: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Deletes a category
     * 
     * @param int $categoryId The ID of the category to delete
     * @throws RepositoryException in case of error
     */
    public function delete(int $categoryId): void {
        try {
            $this->beginTransaction();
            $stmt = $this->pdo->prepare(
                "DELETE FROM categories WHERE category_id = :categoryId"
            );
            $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to delete category record");
            }
            $this->commit();
        } catch (\Throwable $e) {
            if ($this->inTransaction()) {
                $this->rollback();
            }
            throw new RepositoryException("Failed to delete category: " . $e->getMessage(), 0, $e);
        }
    }

    protected function rowToDTO(array $row): CategoryDTO {
        return new CategoryDTO(
            (int)$row['category_id'],
            $row['category_name']
        );
    }
}

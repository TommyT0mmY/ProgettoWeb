<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\CategoryDTO;
use Unibostu\Core\Database;
use PDO;

class CategoryRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera una categoria tramite ID
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
     * Recupera una categoria tramite nome
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
     * Recupera tutte le categorie
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
     * Salva una nuova categoria
     * @throws \Exception in caso di errore
     */
    public function save(string $categoryName): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (category_name)
             VALUES (:categoryName)"
        );
        $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante il salvataggio della categoria");
        }
    }

    /**
     * Aggiorna una categoria
     * @throws \Exception in caso di errore
     */
    public function update(int $categoryId, string $categoryName): void {
        $stmt = $this->pdo->prepare(
            "UPDATE categories SET category_name = :categoryName WHERE category_id = :categoryId"
        );
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'aggiornamento della categoria");
        }
    }

    /**
     * Elimina una categoria
     * @throws \Exception in caso di errore
     */
    public function delete(int $categoryId): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM categories WHERE category_id = :categoryId"
        );
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'eliminazione della categoria");
        }
    }

    private function rowToDTO(array $row): CategoryDTO {
        return new CategoryDTO(
            (int)$row['category_id'],
            $row['category_name']
        );
    }
}

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
    public function findById(int $idcategoria): ?CategoryDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categorie WHERE idcategoria = :idcategoria"
        );
        $stmt->bindValue(':idcategoria', $idcategoria, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera tutte le categorie
     */
    public function findAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM categorie ORDER BY idcategoria"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Salva una nuova categoria
     * @throws \Exception in caso di errore
     */
    public function save(string $nome_categoria): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categorie (nome_categoria)
             VALUES (:nome_categoria)"
        );
        $stmt->bindValue(':nome_categoria', $nome_categoria, PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante il salvataggio della categoria");
        }
    }

    /**
     * Aggiorna una categoria
     * @throws \Exception in caso di errore
     */
    public function update(int $idcategoria, string $nome_categoria): void {
        $stmt = $this->pdo->prepare(
            "UPDATE categorie SET nome_categoria = :nome_categoria WHERE idcategoria = :idcategoria"
        );
        $stmt->bindValue(':idcategoria', $idcategoria, PDO::PARAM_INT);
        $stmt->bindValue(':nome_categoria', $nome_categoria, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'aggiornamento della categoria");
        }
    }

    /**
     * Elimina una categoria
     * @throws \Exception in caso di errore
     */
    public function delete(int $idcategoria): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM categorie WHERE idcategoria = :idcategoria"
        );
        $stmt->bindValue(':idcategoria', $idcategoria, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'eliminazione della categoria");
        }
    }

    private function rowToDTO(array $row): CategoryDTO {
        return new CategoryDTO(
            (int)$row['idcategoria'],
            $row['nome_categoria']
        );
    }
}

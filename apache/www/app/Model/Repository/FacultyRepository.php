<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\Entity\FacultyEntity;
use Unibostu\Core\Database;
use PDO;

class FacultyRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera una facolta tramite ID
     */
    public function findById(int $idfacolta): ?FacultyEntity {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM facolta WHERE idfacolta = :idfacolta"
        );
        $stmt->bindValue(':idfacolta', $idfacolta, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToEntity($row) : null;
    }

    /**
     * Recupera tutte le facolta
     */
    public function findAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM facolta ORDER BY nome_facolta"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToEntity'], $rows);
    }

    /**
     * Salva una nuova facolta
     */
    public function save(FacultyEntity $faculty): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO facolta (nome_facolta)
             VALUES (:nome_facolta)"
        );
        $stmt->bindValue(':nome_facolta', $faculty->nome_facolta, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Aggiorna i dati di una facolta
     */
    public function update(FacultyEntity $faculty): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE facolta 
             SET nome_facolta = :nome_facolta
             WHERE idfacolta = :idfacolta"
        );
        $stmt->bindValue(':nome_facolta', $faculty->nome_facolta, PDO::PARAM_STR);
        $stmt->bindValue(':idfacolta', $faculty->idfacolta, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Elimina una facolta
     */
    public function delete(int $idfacolta): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM facolta WHERE idfacolta = :idfacolta"
        );
        $stmt->bindValue(':idfacolta', $idfacolta, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function rowToEntity(array $row): FacultyEntity {
        return new FacultyEntity(
            (int)$row['idfacolta'],
            $row['nome_facolta']
        );
    }
}


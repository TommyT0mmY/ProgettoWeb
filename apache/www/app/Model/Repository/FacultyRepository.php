<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\FacultyDTO;
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
    public function findById(int $idfacolta): ?FacultyDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM facolta WHERE idfacolta = :idfacolta"
        );
        $stmt->bindValue(':idfacolta', $idfacolta, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
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

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Salva una nuova facolta
     * @throws \Exception in caso di errore
     */
    public function save(string $nome_facolta): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO facolta (nome_facolta)
             VALUES (:nome_facolta)"
        );
        $stmt->bindValue(':nome_facolta', $nome_facolta, PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante il salvataggio della facoltà");
        }
    }

    /**
     * Aggiorna i dati di una facolta
     * @throws \Exception in caso di errore
     */
    public function update(int $idfacolta, string $nome_facolta): void {
        $stmt = $this->pdo->prepare(
            "UPDATE facolta 
             SET nome_facolta = :nome_facolta
             WHERE idfacolta = :idfacolta"
        );
        $stmt->bindValue(':nome_facolta', $nome_facolta, PDO::PARAM_STR);
        $stmt->bindValue(':idfacolta', $idfacolta, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'aggiornamento della facoltà");
        }
    }

    /**
     * Elimina una facolta
     * @throws \Exception in caso di errore
     */
    public function delete(int $idfacolta): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM facolta WHERE idfacolta = :idfacolta"
        );
        $stmt->bindValue(':idfacolta', $idfacolta, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'eliminazione della facoltà");
        }
    }

    private function rowToDTO(array $row): FacultyDTO {
        return new FacultyDTO(
            (int)$row['idfacolta'],
            $row['nome_facolta']
        );
    }
}


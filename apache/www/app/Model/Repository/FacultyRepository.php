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
    public function findById(int $facultyId): ?FacultyDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM faculties WHERE faculty_id = :facultyId"
        );
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera tutte le facolta
     */
    public function findAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM faculties ORDER BY faculty_name"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Salva una nuova facolta
     * @throws \Exception in caso di errore
     */
    public function save(string $facultyName): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO faculties (faculty_name)
             VALUES (:facultyName)"
        );
        $stmt->bindValue(':facultyName', $facultyName, PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante il salvataggio della facoltà");
        }
    }

    /**
     * Aggiorna i dati di una facolta
     * @throws \Exception in caso di errore
     */
    public function update(int $facultyId, string $facultyName): void {
        $stmt = $this->pdo->prepare(
            "UPDATE faculties 
             SET faculty_name = :facultyName
             WHERE faculty_id = :facultyId"
        );
        $stmt->bindValue(':facultyName', $facultyName, PDO::PARAM_STR);
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'aggiornamento della facoltà");
        }
    }

    /**
     * Elimina una facolta
     * @throws \Exception in caso di errore
     */
    public function delete(int $facultyId): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM faculties WHERE faculty_id = :facultyId"
        );
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'eliminazione della facoltà");
        }
    }

    private function rowToDTO(array $row): FacultyDTO {
        return new FacultyDTO(
            (int)$row['faculty_id'],
            $row['faculty_name']
        );
    }
}


<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\FacultyDTO;
use Unibostu\Core\Database;
use PDO;
use RuntimeException;

class FacultyRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Gets faculty details by ID.
     *
     * @param int $facultyId The ID of the faculty
     * @return FacultyDTO|null The FacultyDTO object or null if not found
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
     * Verifies if the faculty exists 
     *
     * @return bool True if the faculty exists, false otherwise
     */
    public function facultyExists(int $facultyId): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM faculties WHERE faculty_id = :facultyId"
        );
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }

    /**
     * Gets all faculties.
     *
     * @return FacultyDTO[] Array of FacultyDTO objects
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
     * Searches faculties by name.
     *
     * @param string $searchTerm The search term
     * @return FacultyDTO[] Array of matching FacultyDTOs
     */
    public function searchByName(string $searchTerm): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM faculties 
            WHERE faculty_name LIKE :searchTerm 
            ORDER BY faculty_name"
        );
        $stmt->bindValue(':searchTerm', $searchTerm . '%', PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Saves a new faculty
     *
     * @throws RuntimeException in case of error
     */
    public function save(string $facultyName): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO faculties (faculty_name)
             VALUES (:facultyName)"
        );
        $stmt->bindValue(':facultyName', $facultyName, PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Errore durante il salvataggio della facoltà");
        }
    }

    /**
     * Updates faculty data
     *
     * @throws RuntimeException in case of error
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
            throw new RuntimeException("Errore durante l'aggiornamento della facoltà");
        }
    }

    /**
     * Deletes a faculty
     *
     * @throws RuntimeException in case of error
     */
    public function delete(int $facultyId): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM faculties WHERE faculty_id = :facultyId"
        );
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new RuntimeException("Errore durante l'eliminazione della facoltà");
        }
    }

    /**
     * Converts a database row to a FacultyDTO
     *
     * @param array $row The database row
     * @return FacultyDTO The FacultyDTO object
     */
    private function rowToDTO(array $row): FacultyDTO {
        return new FacultyDTO(
            (int)$row['faculty_id'],
            $row['faculty_name']
        );
    }
}


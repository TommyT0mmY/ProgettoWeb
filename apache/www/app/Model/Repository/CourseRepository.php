<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\CourseDTO;
use Unibostu\Core\Database;
use PDO;

class CourseRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera un corso tramite ID
     */
    public function findById(int $courseId): ?CourseDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM courses WHERE course_id = :courseId"
        );
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera tutti i corsi
     */
    public function findAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM courses ORDER BY course_name"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Recupera i corsi di una facolta
     */
    public function findByFaculty(int $facultyId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM courses WHERE faculty_id = :facultyId ORDER BY course_name"
        );
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Salva un nuovo corso
     * @throws \Exception in caso di errore
     */
    public function save(string $courseName, int $facultyId): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO courses (course_name, faculty_id)
             VALUES (:courseName, :facultyId)"
        );
        $stmt->bindValue(':courseName', $courseName, PDO::PARAM_STR);
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante il salvataggio del corso");
        }
    }

    /**
     * Aggiorna i dati di un corso
     * @throws \Exception in caso di errore
     */
    public function update(int $courseId, string $courseName, int $facultyId): void {
        $stmt = $this->pdo->prepare(
            "UPDATE courses 
             SET course_name = :courseName, faculty_id = :facultyId
             WHERE course_id = :courseId"
        );
        $stmt->bindValue(':courseName', $courseName, PDO::PARAM_STR);
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'aggiornamento del corso");
        }
    }

    /**
     * Elimina un corso
     * @throws \Exception in caso di errore
     */
    public function delete(int $courseId): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM courses WHERE course_id = :courseId"
        );
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'eliminazione del corso");
        }
    }

    private function rowToDTO(array $row): CourseDTO {
        return new CourseDTO(
            (int)$row['course_id'],
            $row['course_name'],
            (int)$row['faculty_id']
        );
    }
}


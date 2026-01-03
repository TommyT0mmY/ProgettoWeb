<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\CourseDTO;
use Unibostu\Core\Database;
use PDO;

class UserCoursesRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera i corsi di un utente
     * @return CourseDTO[]
     */
    public function findCoursesByUser(string $idutente): array {
        $stmt = $this->pdo->prepare(
            "SELECT c.* FROM corsi c
                JOIN utenti_corsi uc ON c.idcorso = uc.idcorso
                WHERE uc.idutente = :idutente
                ORDER BY c.nome_corso"
        );
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($rows as $row) {
            $courses[] = $this->rowToDTO($row);
        }
        return $courses;
    }

    /**
     * Salva i corsi di un utente
     * @throws \Exception in caso di errore
     */
    public function saveUserCourses(string $idutente, array $courseIds): void {
        // Inizia una transazione
        $this->pdo->beginTransaction();

        try {
            // Rimuovi i corsi esistenti per l'utente
            $deleteStmt = $this->pdo->prepare(
                "DELETE FROM utenti_corsi WHERE idutente = :idutente"
            );
            $deleteStmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
            $deleteStmt->execute();

            // Aggiungi i nuovi corsi
            $insertStmt = $this->pdo->prepare(
                "INSERT INTO utenti_corsi (idutente, idcorso) VALUES (:idutente, :idcorso)"
            );
            foreach ($courseIds as $idcorso) {
                $insertStmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
                $insertStmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
                $insertStmt->execute();
            }

            // Conferma la transazione
            $this->pdo->commit();
        } catch (\Exception $e) {
            // Annulla la transazione in caso di errore
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function rowToDTO(array $row): CourseDTO {
        return new CourseDTO(
            (int)$row['idcorso'],
            $row['nome_corso'],
            (int)$row['idfacolta']
        );
    }
}


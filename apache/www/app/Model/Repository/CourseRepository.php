<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\Entity\CourseEntity;
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
    public function findById(int $idcorso): ?CourseEntity {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM corsi WHERE idcorso = :idcorso"
        );
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToEntity($row) : null;
    }

    /**
     * Recupera tutti i corsi
     */
    public function findAll(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM corsi ORDER BY nome_corso"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToEntity'], $rows);
    }

    /**
     * Recupera i corsi di una facolta
     */
    public function findByFaculty(int $idfacolta): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM corsi WHERE idfacolta = :idfacolta ORDER BY nome_corso"
        );
        $stmt->bindValue(':idfacolta', $idfacolta, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToEntity'], $rows);
    }

    /**
     * Salva un nuovo corso
     */
    public function save(CourseEntity $course): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO corsi (nome_corso, idfacolta)
             VALUES (:nome_corso, :idfacolta)"
        );
        $stmt->bindValue(':nome_corso', $course->nome_corso, PDO::PARAM_STR);
        $stmt->bindValue(':idfacolta', $course->idfacolta, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Aggiorna i dati di un corso
     */
    public function update(CourseEntity $course): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE corsi 
             SET nome_corso = :nome_corso, idfacolta = :idfacolta
             WHERE idcorso = :idcorso"
        );
        $stmt->bindValue(':nome_corso', $course->nome_corso, PDO::PARAM_STR);
        $stmt->bindValue(':idfacolta', $course->idfacolta, PDO::PARAM_INT);
        $stmt->bindValue(':idcorso', $course->idcorso, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Elimina un corso
     */
    public function delete(int $idcorso): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM corsi WHERE idcorso = :idcorso"
        );
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function rowToEntity(array $row): CourseEntity {
        return new CourseEntity(
            (int)$row['idcorso'],
            $row['nome_corso'],
            (int)$row['idfacolta']
        );
    }
}


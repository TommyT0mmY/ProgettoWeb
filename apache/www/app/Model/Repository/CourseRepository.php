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
    public function findById(int $idcorso): ?CourseDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM corsi WHERE idcorso = :idcorso"
        );
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
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

        return array_map([$this, 'rowToDTO'], $rows);
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

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Salva un nuovo corso
     * @throws \Exception in caso di errore
     */
    public function save(string $nome_corso, int $idfacolta): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO corsi (nome_corso, idfacolta)
             VALUES (:nome_corso, :idfacolta)"
        );
        $stmt->bindValue(':nome_corso', $nome_corso, PDO::PARAM_STR);
        $stmt->bindValue(':idfacolta', $idfacolta, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante il salvataggio del corso");
        }
    }

    /**
     * Aggiorna i dati di un corso
     * @throws \Exception in caso di errore
     */
    public function update(int $idcorso, string $nome_corso, int $idfacolta): void {
        $stmt = $this->pdo->prepare(
            "UPDATE corsi 
             SET nome_corso = :nome_corso, idfacolta = :idfacolta
             WHERE idcorso = :idcorso"
        );
        $stmt->bindValue(':nome_corso', $nome_corso, PDO::PARAM_STR);
        $stmt->bindValue(':idfacolta', $idfacolta, PDO::PARAM_INT);
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'aggiornamento del corso");
        }
    }

    /**
     * Elimina un corso
     * @throws \Exception in caso di errore
     */
    public function delete(int $idcorso): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM corsi WHERE idcorso = :idcorso"
        );
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'eliminazione del corso");
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


<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\CommentDTO;
use Unibostu\Model\DTO\CreateCommentDTO;
use Unibostu\Core\Database;
use PDO;

class CommentRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera tutti i commenti di un post (inclusi cancellati soft-deleted)
     */
    public function findByPostId(int $postid): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM commenti 
             WHERE idpost = :idpost
             ORDER BY data_creazione ASC"
        );
        $stmt->bindValue(':idpost', $postid, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $comments = [];
        foreach ($rows as $row) {
            $comments[] = $this->rowToDTO($row);
        }
        return $comments;
    }

    /**
     * Recupera un commento tramite ID
     */
    public function findById(int $idcommento, int $idpost): ?CommentDTO {
        $stmt = $this->pdo->prepare("SELECT * FROM commenti WHERE idcommento = :idcommento AND idpost = :idpost");
        $stmt->bindValue(':idcommento', $idcommento, PDO::PARAM_INT);
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Salva un nuovo commento da DTO
     */
    public function save(CreateCommentDTO $dto): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO commenti 
             (idpost, testo, data_creazione, cancellato, idutente, idcommento_genitore)
             VALUES (:idpost, :testo, :data_creazione, :cancellato, :idutente, :idcommento_genitore)"
        );
        $stmt->bindValue(':idpost', $dto->idpost, PDO::PARAM_INT);
        $stmt->bindValue(':testo', $dto->testo, PDO::PARAM_STR);
        $stmt->bindValue(':data_creazione', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(':cancellato', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':idutente', $dto->idutente, PDO::PARAM_STR);
        $stmt->bindValue(':idcommento_genitore', $dto->idcommento_genitore, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new \Exception("Errore nel salvataggio del commento");
        }
    }

    /**
     * Segna un commento come cancellato (soft delete)
     */
    public function delete(int $idcommento, int $idpost): void {
        $stmt = $this->pdo->prepare(
            "UPDATE commenti SET cancellato = true, testo = :testo 
             WHERE idcommento = :idcommento AND idpost = :idpost"
        );
        $stmt->bindValue(':testo', 'commento cancellato', PDO::PARAM_STR);
        $stmt->bindValue(':idcommento', $idcommento, PDO::PARAM_INT);
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new \Exception("Errore nella cancellazione del commento");
        }
    }

    private function rowToDTO(array $row): CommentDTO {
        $dto = new CommentDTO(
            (int)$row['idcommento'],
            (int)$row['idpost'],
            $row['testo'],
            $row['data_creazione'],
            (string)$row['idutente'],
            (bool)$row['cancellato'],
            $row['idcommento_genitore'] ? (int)$row['idcommento_genitore'] : null
        );
        return $dto;
    }
}
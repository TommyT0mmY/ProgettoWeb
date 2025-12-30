<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\Entity\CommentEntity;
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
            $comments[] = $this->rowToEntity($row);
        }
        return $comments;
    }

    /**
     * Salva un nuovo commento
     */
    public function save(CommentEntity $comment): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO commenti 
             (idpost, idcommento, testo, data_creazione, cancellato, identita, idpost_genitore, idcommento_genitore)
             VALUES (:idpost, :idcommento, :testo, :data_creazione, :cancellato, :identita, :idpost_genitore, :idcommento_genitore)"
        );
        $stmt->bindValue(':idpost', $comment->idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idcommento', $comment->idcommento, PDO::PARAM_INT);
        $stmt->bindValue(':testo', $comment->testo, PDO::PARAM_STR);
        $stmt->bindValue(':data_creazione', $comment->data_creazione, PDO::PARAM_STR);
        $stmt->bindValue(':cancellato', $comment->cancellato, PDO::PARAM_BOOL);
        $stmt->bindValue(':identita', $comment->identita, PDO::PARAM_INT);
        $stmt->bindValue(':idpost_genitore', $comment->idpost_genitore, PDO::PARAM_INT);
        $stmt->bindValue(':idcommento_genitore', $comment->idcommento_genitore, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Segna un commento come cancellato (soft delete)
     */
    public function delete(int $idpost, int $idcommento): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE commenti SET cancellato = true, testo = :testo 
             WHERE idpost = :idpost AND idcommento = :idcommento"
        );
        $stmt->bindValue(':testo', 'commento cancellato', PDO::PARAM_STR);
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idcommento', $idcommento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function rowToEntity(array $row): CommentEntity {
        return new CommentEntity(
            (int)$row['idpost'],
            (int)$row['idcommento'],
            $row['testo'],
            $row['data_creazione'],
            (bool)$row['cancellato'],
            (int)$row['identita'],
            (int)$row['idpost_genitore'],
            (int)$row['idcommento_genitore']
        );
    }
}

?>
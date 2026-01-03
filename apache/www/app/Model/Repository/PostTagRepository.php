<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Core\Database;
use PDO;

class PostTagRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera tutti i tag di un post
     */
    public function findTagsByPost(int $idpost): array {
        $stmt = $this->pdo->prepare(
            "SELECT idtag, idcorso FROM post_tags WHERE idpost = :idpost"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupera i post di un tag specifico
     */
    public function findPostsByTag(int $idtag): array {
        $stmt = $this->pdo->prepare(
            "SELECT idpost FROM post_tags WHERE idtag = :idtag"
        );
        $stmt->bindValue(':idtag', $idtag, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aggiunge un tag a un post
     */
    public function addTagToPost(int $idpost, int $idtag, int $idcorso): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO post_tags (idpost, idtag, idcorso)
             VALUES (:idpost, :idtag, :idcorso)"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idtag', $idtag, PDO::PARAM_INT);
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Rimuove un tag da un post
     */
    public function removeTagFromPost(int $idpost, int $idtag): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM post_tags WHERE idpost = :idpost AND idtag = :idtag"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idtag', $idtag, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

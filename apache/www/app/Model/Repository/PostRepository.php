<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\Entity\PostEntity;
use Unibostu\Core\Database;
use PDO;

class PostRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera un post tramite ID
     */
    public function findById(int $idpost): ?PostEntity {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE idpost = :idpost");
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToEntity($row) : null;
    }

    /**
     * Recupera tutti i post ordinati per data recente
     */
    public function findAll(): array {
        $stmt = $this->pdo->query(
            "SELECT * FROM posts ORDER BY data_creazione DESC LIMIT 50"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = $this->rowToEntity($row);
        }
        return $posts;
    }

    /**
     * Recupera i post di un utente
     */
    public function findByUserId(int $identita): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM posts 
             WHERE identita = :identita
             ORDER BY data_creazione DESC"
        );
        $stmt->bindValue(':identita', $identita, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = $this->rowToEntity($row);
        }
        return $posts;
    }

    /**
     * Recupera i post di un corso
     */
    public function findByCourseId(int $idcorso): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM posts 
             WHERE idcorso = :idcorso
             ORDER BY data_creazione DESC"
        );
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = $this->rowToEntity($row);
        }
        return $posts;
    }

    /**
     * Salva un nuovo post
     */
    public function save(PostEntity $post): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO posts 
             (titolo, descrizione, percorso_allegato, likes, dislikes, data_creazione, identita, idcorso)
             VALUES (:titolo, :descrizione, :percorso_allegato, :likes, :dislikes, :data_creazione, :identita, :idcorso)"
        );
        $stmt->bindValue(':titolo', $post->titolo, PDO::PARAM_STR);
        $stmt->bindValue(':descrizione', $post->descrizione, PDO::PARAM_STR);
        $stmt->bindValue(':percorso_allegato', $post->percorso_allegato, PDO::PARAM_STR);
        $stmt->bindValue(':likes', $post->likes, PDO::PARAM_INT);
        $stmt->bindValue(':dislikes', $post->dislikes, PDO::PARAM_INT);
        $stmt->bindValue(':data_creazione', $post->data_creazione, PDO::PARAM_STR);
        $stmt->bindValue(':identita', $post->identita, PDO::PARAM_INT);
        $stmt->bindValue(':idcorso', $post->idcorso, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Incrementa i like di un post
     */
    public function incrementLikes(int $idpost): bool {
        $stmt = $this->pdo->prepare("UPDATE posts SET likes = likes + 1 WHERE idpost = :idpost");
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Incrementa i dislike di un post
     */
    public function incrementDislikes(int $idpost): bool {
        $stmt = $this->pdo->prepare("UPDATE posts SET dislikes = dislikes + 1 WHERE idpost = :idpost");
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Elimina un post
     */
    public function delete(int $idpost): bool {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE idpost = :idpost");
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function rowToEntity(array $row): PostEntity {
        return new PostEntity(
            (int)$row['idpost'],
            $row['titolo'],
            $row['descrizione'],
            $row['percorso_allegato'],
            (int)$row['likes'],
            (int)$row['dislikes'],
            $row['data_creazione'],
            (int)$row['identita'],
            $row['idcorso'] ? (int)$row['idcorso'] : null
        );
    }
}
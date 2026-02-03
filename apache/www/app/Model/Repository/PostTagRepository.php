<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Core\Database;
use Unibostu\Model\DTO\TagDTO;
use PDO;

class PostTagRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera tutti i tag di un post
     * @return array Array di array con chiavi TagDTO
     */
    public function findTagsByPost(int $postId): array {
        $stmt = $this->pdo->prepare(
            "SELECT t.tag_id, t.tag_name, t.course_id
             FROM tags t
             JOIN post_tags pt ON t.tag_id = pt.tag_id
             WHERE pt.post_id = :postId
             ORDER BY t.tag_name"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupera i post di un tag specifico
     */
    public function findPostsByTag(int $tagId): array {
        $stmt = $this->pdo->prepare(
            "SELECT post_id FROM post_tags WHERE tag_id = :tagId"
        );
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aggiunge un tag a un post
     */
    public function addTagToPost(int $postId, int $tagId): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO post_tags (post_id, tag_id)
             VALUES (:postId, :tagId)"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Rimuove un tag da un post
     */
    public function removeTagFromPost(int $postId, int $tagId): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM post_tags WHERE post_id = :postId AND tag_id = :tagId"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function rowToDTO(array $row): TagDTO {
        return new TagDTO(
            (int)$row['tag_id'],
            $row['tag_name'],
            (int)$row['course_id']
        );
    }
}

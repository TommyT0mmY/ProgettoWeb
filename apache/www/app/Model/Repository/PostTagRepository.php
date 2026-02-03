<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\TagDTO;
use PDO;

class PostTagRepository extends BaseRepository {

    /**
     * Retrieves all tags for a post
     * 
     * @param int $postId The ID of the post
     * @return array Array of arrays with TagDTO keys
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
     * Retrieves posts for a specific tag
     * 
     * @param int $tagId The ID of the tag
     * @return array Array of arrays with post_id keys
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
     * Adds a tag to a post
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
     * Removes a tag from a post
     */
    public function removeTagFromPost(int $postId, int $tagId): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM post_tags WHERE post_id = :postId AND tag_id = :tagId"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    protected function rowToDTO(array $row): TagDTO {
        return new TagDTO(
            (int)$row['tag_id'],
            $row['tag_name'],
            (int)$row['course_id']
        );
    }
}

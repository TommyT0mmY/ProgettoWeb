<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\TagDTO;
use Unibostu\Core\exceptions\RepositoryException;
use PDO;

class TagRepository extends BaseRepository {
    /**
     * Retrieves a tag by ID and course
     * 
     * @param int $tagId The ID of the tag
     * @param int $courseId The ID of the course
     * @return TagDTO|null The TagDTO object or null if not found
     */
    public function findByIdAndCourse(int $tagId, int $courseId): ?TagDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE tag_id = :tagId AND course_id = :courseId"
        );
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Retrieves a tag by ID
     * 
     * @param int $tagId The ID of the tag
     * @return TagDTO|null The TagDTO object or null if not found
     */
    public function findById(int $tagId): ?TagDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE tag_id = :tagId"
        );
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Retrieves a tag by name and course
     * 
     * @param string $tagName The name of the tag
     * @param int $courseId The ID of the course
     * @return TagDTO|null The TagDTO object or null if not found
     */
    public function findByTypeAndCourse(string $tagName, int $courseId): ?TagDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE tag_name = :tagName AND course_id = :courseId"
        );
        $stmt->bindValue(':tagName', $tagName, PDO::PARAM_STR);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Retrieves all tags for a course
     * 
     * @param int $courseId The ID of the course
     * @return TagDTO[] Array of TagDTO objects
     */
    public function findByCourse(int $courseId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE course_id = :courseId ORDER BY tag_name"
        );
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Searches tags by name.
     *
     * @param string $searchTerm The search term
     * @return TagDTO[] Array of matching TagDTOs
     */
    public function searchByName(string $searchTerm): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags 
            WHERE tag_name LIKE :searchTerm 
            ORDER BY tag_name"
        );
        $stmt->bindValue(':searchTerm', $searchTerm . '%', PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Saves a new tag
     * 
     * @param string $tagName The name of the tag
     * @param int $courseId The ID of the course
     * @throws RepositoryException
     */
    public function save(string $tagName, int $courseId): void {
        $this->executeInTransaction(function() use ($tagName, $courseId) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tags (tag_name, course_id)
                 VALUES (:tagName, :courseId)"
            );
            $stmt->bindValue(':tagName', $tagName, PDO::PARAM_STR);
            $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to save tag");
            }
        });
    }

    /**
     * Updates a tag
     * 
     * @param int $tagId The ID of the tag to update
     * @param string $tagName The new name of the tag
     * @throws RepositoryException
     */
    public function update(int $tagId, string $tagName): void {
        $this->executeInTransaction(function() use ($tagId, $tagName) {
            $stmt = $this->pdo->prepare(
                "UPDATE tags SET tag_name = :tagName WHERE tag_id = :tagId"
            );
            $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
            $stmt->bindValue(':tagName', $tagName, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to update tag");
            }
        });
    }

    /**
     * Deletes a tag
     * 
     * @param int $tagId The ID of the tag to delete
     * @throws RepositoryException
     */
    public function delete(int $tagId): void {
        $this->executeInTransaction(function() use ($tagId) {
            $stmt = $this->pdo->prepare(
                "DELETE FROM tags WHERE tag_id = :tagId"
            );
            $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to delete tag");
            }
        });
    }

    protected function rowToDTO(array $row): TagDTO {
        return new TagDTO(
            (int)$row['tag_id'],
            $row['tag_name'],
            (int)$row['course_id']
        );
    }
}

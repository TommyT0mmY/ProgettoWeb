<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\TagDTO;
use Unibostu\Core\Database;
use PDO;

class TagRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }



    /**
     * Recupera un tag tramite tipo e corso
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
     * Recupera un tag tramite tipo e corso
     */
    public function findByTypeAndCourse(string $tag_name, int $courseId): ?TagDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE tag_name = :tag_name AND course_id = :courseId"
        );
        $stmt->bindValue(':tag_name', $tag_name, PDO::PARAM_STR);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera tutti i tag di un corso
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
     * Salva un nuovo tag
     * @throws \Exception in caso di errore
     */
    public function save(string $tag_name, int $courseId): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tags (tag_name, course_id)
             VALUES (:tag_name, :courseId)"
        );
        $stmt->bindValue(':tag_name', $tag_name, PDO::PARAM_STR);
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante il salvataggio del tag");
        }
    }

    /**
     * Aggiorna un tag
     * @throws \Exception in caso di errore
     */
    public function update(int $tagId, string $tag_name): void {
        $stmt = $this->pdo->prepare(
            "UPDATE tags SET tag_name = :tag_name WHERE tag_id = :tagId"
        );
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        $stmt->bindValue(':tag_name', $tag_name, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'aggiornamento del tag");
        }
    }

    /**
     * Elimina un tag
     * @throws \Exception in caso di errore
     */
    public function delete(int $tagId): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM tags WHERE tag_id = :tagId"
        );
        $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'eliminazione del tag");
        }
    }

    private function rowToDTO(array $row): TagDTO {
        return new TagDTO(
            (int)$row['tag_id'],
            $row['tag_name'],
            (int)$row['course_id']
        );
    }
}

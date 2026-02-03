<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Dom\Comment;
use Unibostu\Model\DTO\CommentDTO;
use Unibostu\Model\DTO\CreateCommentDTO;
use Unibostu\Core\Database;
use PDO;
use Unibostu\Model\DTO\UserDTO;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;

class CommentRepository {
    private PDO $pdo;
    private UserRepository $userRepository;

    public function __construct() {
        $this->pdo = Database::getConnection();
        $this->userRepository = new UserRepository();
    }

    /**
     * Retrieves all comments of a post (including soft-deleted)
     */
    public function findByPostId(int $postId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM comments 
             WHERE post_id = :postId
             ORDER BY created_at ASC"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $comments = [];
        foreach ($rows as $row) {
            $comments[] = $this->rowToDTO($row);
        }
        return $comments;
    }

    /**
     * Retrieves a comment by ID
     */
    public function findById(int $commentId, int $postId): ?CommentDTO {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE comment_id = :commentId AND post_id = :postId");
        $stmt->bindValue(':commentId', $commentId, PDO::PARAM_INT);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Salva un nuovo commento da DTO
     */
    public function save(CreateCommentDTO $dto): CommentDTO {
        $stmt = $this->pdo->prepare(
            "INSERT INTO comments 
             (post_id, comment_text, created_at, deleted, user_id, parent_comment_id)
             VALUES (:postId, :text, :createdAt, :deleted, :userId, :parentCommentId)"
        );
        $stmt->bindValue(':postId', $dto->postId, PDO::PARAM_INT);
        $stmt->bindValue(':text', $dto->text, PDO::PARAM_STR);
        $stmt->bindValue(':createdAt', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(':deleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':userId', $dto->userId, PDO::PARAM_STR);
        $stmt->bindValue(':parentCommentId', $dto->parentCommentId, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new \Exception("Errore nel salvataggio del commento");
        }
        return $this->lastInsertedComment();
    }

    /**
     * Segna un commento come cancellato (soft delete)
     */
    public function delete(int $commentId, int $postId): void {
        $stmt = $this->pdo->prepare(
            "UPDATE comments SET deleted = true, comment_text = :text 
             WHERE comment_id = :commentId AND post_id = :postId"
        );
        $stmt->bindValue(':text', 'comment deleted', PDO::PARAM_STR);
        $stmt->bindValue(':commentId', $commentId, PDO::PARAM_INT);
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new \Exception("Errore nella cancellazione del commento");
        }
    }

    private function lastInsertedComment(): CommentDTO {
        $stmt = $this->pdo->query("SELECT * FROM comments ORDER BY comment_id DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->rowToDTO($row);
    }

    private function rowToDTO(array $row): CommentDTO {
        $author = $this->userRepository->findByUserId($row['user_id']);
        $dto = new CommentDTO(
            author: $author,
            commentId: (int)$row['comment_id'],
            postId: (int)$row['post_id'],
            text: $row['comment_text'],
            createdAt: $row['created_at'],
            deleted: (bool)$row['deleted'],
            parentCommentId: $row['parent_comment_id'] !== null ? (int)$row['parent_comment_id'] : null
        );
        return $dto;
    }
}
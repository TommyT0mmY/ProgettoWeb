<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\PostDTO;
use Unibostu\Model\DTO\PostFilterDTO;
use Unibostu\Model\DTO\CreatePostDTO;

use Unibostu\Core\Database;
use PDO;

class PostRepository {
    private PDO $pdo;
    private PostTagRepository $postTagRepository;
    private UserRepository $userRepository;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
        $this->postTagRepository = new PostTagRepository();
        $this->userRepository = new UserRepository();
    }

    /**
     * Recupera un post tramite ID
     */
    public function findById(int $postId): ?PostDTO {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE post_id = :postId");
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera post con filtri applicati
     */     
    public function findWithFilters(string $userId, ?PostFilterDTO $filter): array {
        $sql = "SELECT DISTINCT p.* FROM posts p";
        $conditions = [];
        $params = [];

        // se filtro è null ritorna tutti i post
        if ($filter !== null) {
            // Filtro per categoria
            if (!empty($filter->category)) {
                $params[] = $filter->category;
                $conditions[] = " p.category_id = ?";
            }

            // Join per tag se filtrati
            if (!empty($filter->tags)) {
                $sql .= " LEFT JOIN post_tags pt ON p.post_id = pt.post_id";
                $tagConditions = [];
                foreach ($filter->tags as $tag) {
                    $tagConditions[] = "(pt.tag_id = ? AND pt.course_id = ?)";
                    $params[] = $tag['tagId'];
                    $params[] = $tag['courseId'];
                }
                if (!empty($tagConditions)) {
                    $conditions[] = "(" . implode(" OR ", $tagConditions) . ")";
                }
            }

            // Filtro per corsi
            if (!empty($filter->courseId)) {
                $params[] = $filter->courseId;
                $conditions[] = " p.course_id IN (?)";
            } else {
                $params[] = $userId;
                $conditions[] = " p.course_id IN (
                    SELECT course_id FROM user_courses WHERE user_id = ?
                )";
            }

            // Filtro per autore
            if (!empty($filter->authorId)) {
                $params[] = $filter->authorId;
                $conditions[] = " p.user_id = ?";
            }

            // Aggiungi WHERE clause se ci sono condizioni
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            if ($filter->sortOrder === 'ASC') {
                $sql .= " AND p.post_id > ?";
            } else {
                $sql .= " AND p.post_id < ?";
            }
            $params[] = $filter->lastPostId;

            // Ordinamento
            $sql .= " ORDER BY p.created_at " . $filter->sortOrder;

            // Limit e offset
            $sql .= " LIMIT ?";
            $params[] = $filter->limit;
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key + 1, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key + 1, $value, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            //Recupera autore post
            $author = $this->userRepository->findByUserId($row['user_id']);
            $row['author'] = $author;

            $posts[] = $this->rowToDTO($row);
        }
        return $posts;
    }

    /**
     * Recupera i post di un utente
     */
    public function findByUserId(string $userId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM posts 
             WHERE user_id = :userId
             ORDER BY created_at DESC"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = $this->rowToDTO($row);
        }
        return $posts;
    }

    /**
     * Recupera i post di un corso
     */
    public function findByCourseId(int $courseId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM posts 
             WHERE course_id = :courseId
             ORDER BY created_at DESC"
        );
        $stmt->bindValue(':courseId', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];
        foreach ($rows as $row) {
            $posts[] = $this->rowToDTO($row);
        }
        return $posts;
    }

    /**
     * Salva un nuovo post
     */
    public function save(CreatePostDTO $dto): void {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "INSERT INTO posts 
                (title, description, attachment_path, created_at, user_id, course_id, category_id)
                VALUES (:title, :description, :attachmentPath, :createdAt, :userId, :courseId, :category)"
            );
            $stmt->bindValue(':title', $dto->title, PDO::PARAM_STR);
            $stmt->bindValue(':description', $dto->description, PDO::PARAM_STR);
            $stmt->bindValue(':attachmentPath', $dto->attachmentPath ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':createdAt', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(':userId', $dto->userId, PDO::PARAM_STR);
            $stmt->bindValue(':courseId', $dto->courseId ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':category', $dto->category ?? null, PDO::PARAM_INT);
            $stmt->execute();
            $postId = (int)$this->pdo->lastInsertId();

            // Salva i tag
            if (!empty($dto->tags)) {
                foreach ($dto->tags as $tag) {
                    $this->postTagRepository->addTagToPost($postId, $tag['tagId'], $tag['courseId']);
                }
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Aggiunge un voto (like/dislike) da parte di un utente a un post
     * Usa la tabella likes per tracciare chi ha votato
     * 
     * @param int $postId ID del post
     * @param string $userId ID dell'utente
     * @param bool $isLike true per like, false per dislike
     */
    public function setReaction(int $postId, string $userId, bool $isLike): void {
        // Verifica se l'utente ha già votato
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM likes WHERE post_id = :postId AND user_id = :userId"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ((int)$result['count'] > 0) {
            $stmtUpdate = $this->pdo->prepare(
                "UPDATE likes SET is_like = :is_like WHERE post_id = :postId AND user_id = :userId"
            );
            $stmtUpdate->bindValue(':is_like', $isLike ? 1 : 0, PDO::PARAM_INT);
            $stmtUpdate->bindValue(':postId', $postId, PDO::PARAM_INT);
            $stmtUpdate->bindValue(':userId', $userId, PDO::PARAM_STR);
            if (!$stmtUpdate->execute()) {
                throw new \Exception("Errore durante l'aggiornamento del voto");
            }
        } else {
            // Inserisci il voto nella tabella likes
            $stmtVote = $this->pdo->prepare(
                "INSERT INTO likes (post_id, user_id, is_like) VALUES (:postId, :userId, :is_like)"
            );
            $stmtVote->bindValue(':postId', $postId, PDO::PARAM_INT);
            $stmtVote->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmtVote->bindValue(':is_like', $isLike ? 1 : 0, PDO::PARAM_INT);
            
            if (!$stmtVote->execute()) {
                throw new \Exception("Errore durante il salvataggio del voto");
            }
        }
    }

    /**
     * Rimuove un voto (like/dislike) da parte di un utente da un post
     * Usa la tabella likes per tracciare chi ha tolto il voto
     * 
     * @param int $postId ID del post
     * @param string $userId ID dell'utente
     */
    public function removeReaction(int $postId, string $userId): bool {
        // Rimuovi il voto dalla tabella likes
        $stmtDelete = $this->pdo->prepare(
            "DELETE FROM likes WHERE post_id = :postId AND user_id = :userId"
        );
        $stmtDelete->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmtDelete->bindValue(':userId', $userId, PDO::PARAM_STR);
        
        if (!$stmtDelete->execute()) {
            throw new \Exception("Errore durante la rimozione del voto");
        }
        
        return true;
    }

    /**
     * Conta i like di un post
     */
    public function countLikes(int $postId): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE post_id = :postId AND is_like = 1");
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Conta i dislike di un post
     */
    public function countDislikes(int $postId): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE post_id = :postId AND is_like = 0");
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Verifica che l'utente ha messo like o dislike
     */
    private function hasUserVoted(int $postId, string $userId): ?bool {
        $stmt = $this->pdo->prepare(
            "SELECT is_like FROM likes WHERE post_id = :postId AND user_id = :userId"
        );
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return (bool)$result['is_like'];
    }

    /**
     * Elimina un post
     */
    public function delete(int $postId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE post_id = :postId");
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verifica che l'utente ha messo like o dislike
     */

    private function rowToDTO(array $row): PostDTO {
        $postId = (int)$row['post_id'];
        $tags = $this->postTagRepository->findTagsByPost($postId);
        $likes = $this->countLikes($postId);
        $dislikes = $this->countDislikes($postId);
        $likedByCurrentUser = $this->hasUserVoted($postId, $row['user_id']);

        $dto = new PostDTO(
            postId: $postId,
            title: $row['title'],
            description: $row['description'],
            createdAt: $row['created_at'],
            userId: $row['user_id'],
            courseId: (int)$row['course_id'],
            tags: $tags,
            category: $row['category_id'],
            likes: $likes,
            dislikes: $dislikes,
            likedByUser: $likedByCurrentUser,
            attachmentPath: $row['attachment_path'] ?? null
        );
        return $dto;
    }
}



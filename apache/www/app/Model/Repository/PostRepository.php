<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\PostDTO;
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Model\DTO\CreatePostDTO;

use Unibostu\Core\Database;
use PDO;

class PostRepository {
    private PDO $pdo;
    private PostTagRepository $postTagRepository;
    private UserRepository $userRepository;
    private CourseRepository $courseRepository;
    private CategoryRepository $categoryRepository;
    private AttachmentRepository $attachmentRepository;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
        $this->postTagRepository = new PostTagRepository();
        $this->userRepository = new UserRepository();
        $this->courseRepository = new CourseRepository();
        $this->categoryRepository = new CategoryRepository();
        $this->attachmentRepository = new AttachmentRepository();
    }

    /**
     * Finds a post by its ID
     *
     * @param int $postId The ID of the post to find
     * @param string|null $currentUserId ID dell'utente corrente per determinare likedByUser
     * @return PostDTO|null The PostDTO if found, null otherwise
     */
    public function findById(int $postId, ?string $currentUserId = null): ?PostDTO {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE post_id = :postId");
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $author = $this->userRepository->findByUserId($row['user_id']);
        $row['author'] = $author;

        return $this->rowToDTO($row, $currentUserId);
    }

    /**
     * Finds posts with optional filters
     *
     * @param string $userId The ID of the user requesting the posts
     * @param PostQuery $postQuery The query object containing filter criteria
     */
    public function findWithFilters(PostQuery $postQuery): array {
        $sql = "SELECT DISTINCT p.* FROM posts p";
        $conditions = [];
        $params = [];
        $needsGroupBy = false;
        $tagCount = 0;

        // Filtro per categoria (disponibile per utenti e admin)
        if (!empty($postQuery->getCategory())) {
            $conditions[] = " p.category_id = ?";
            $params[] = $postQuery->getCategory();
        }

        // Join per tag se filtrati - trova post che hanno TUTTI i tag (disponibile per utenti e admin)
        if (!empty($postQuery->getTags())) {
            $sql .= " INNER JOIN post_tags pt ON p.post_id = pt.post_id";
            $tagConditions = [];
            foreach ($postQuery->getTags() as $tag) {
                $tagConditions[] = "(pt.tag_id = ? AND pt.course_id = ?)";
                $params[] = $tag['tagId'];
                $params[] = $tag['courseId'];
            }
            if (!empty($tagConditions)) {
                $conditions[] = "(" . implode(" OR ", $tagConditions) . ")";
            }
            $needsGroupBy = true;
            $tagCount = count($postQuery->getTags());
        }

        // Filtro per corsi (disponibile per utenti e admin)
        if (!empty($postQuery->getCourseId())) {
            $params[] = $postQuery->getCourseId();
            $conditions[] = " p.course_id IN (?)";
        } else if (!empty($postQuery->getUserId()) && $postQuery->getIsAdminView() === false) {
            // Solo gli utenti normali hanno i post limitati ai loro corsi
            $params[] = $postQuery->getUserId();
            $conditions[] = " p.course_id IN (
                SELECT course_id FROM user_courses WHERE user_id = ?
            )";
        }

        // Filtro per autore (disponibile per utenti e admin)
        if (!empty($postQuery->getAuthorId())) {
            $params[] = $postQuery->getAuthorId();
            $conditions[] = " p.user_id = ?";
        }

        $sql .= " WHERE ";
        if (!empty($conditions)) {
            $sql .= implode(" AND ", $conditions) . " AND ";
        }

        // Paginazione
        if ($postQuery->getSortOrder() === 'ASC') {
            $sql .= " p.post_id > ?";
        } else {
            $sql .= " p.post_id < ?";
        }
        $params[] = $postQuery->getLastPostId();

        // GROUP BY e HAVING per assicurare che il post abbia TUTTI i tag richiesti
        if ($needsGroupBy) {
            $sql .= " GROUP BY p.post_id HAVING COUNT(DISTINCT pt.tag_id) = ?";
            $params[] = $tagCount;
        }

        // Ordinamento
        $sql .= " ORDER BY p.created_at " . $postQuery->getSortOrder();

        // Limit e offset
        $sql .= " LIMIT ?";
        $params[] = $postQuery->getLimit();

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

            $posts[] = $this->rowToDTO($row, $postQuery->getUserId());
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
            $posts[] = $this->rowToDTO($row, null);
        }
        return $posts;
    }

    /**
     * Saves a new post
     * @return int The created post ID
     */
    public function save(CreatePostDTO $dto): int {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare(
                "INSERT INTO posts 
                (title, description, created_at, user_id, course_id, category_id)
                VALUES (:title, :description, :createdAt, :userId, :courseId, :category)"
            );
            $stmt->bindValue(':title', $dto->title, PDO::PARAM_STR);
            $stmt->bindValue(':description', $dto->description, PDO::PARAM_STR);
            $stmt->bindValue(':createdAt', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(':userId', $dto->userId, PDO::PARAM_STR);
            $stmt->bindValue(':courseId', $dto->courseId ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':category', $dto->category ?? null, PDO::PARAM_INT);
            $stmt->execute();
            $postId = (int)$this->pdo->lastInsertId();
            if (!empty($dto->tags)) { // Save tags
                foreach ($dto->tags as $tag) {
                    $this->postTagRepository->addTagToPost($postId, $tag['tagId'], $tag['courseId']);
                }
            }
            $this->pdo->commit();
            return $postId;
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
     * Ottiene la reazione dell'utente per un post
     * @return string|null 'like', 'dislike' o null
     */
    public function getUserReaction(int $postId, string $userId): ?string {
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

        return (bool)$result['is_like'] ? 'like' : 'dislike';
    }

    /**
     * Elimina un post
     */
    public function delete(int $postId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE post_id = :postId");
        $stmt->bindValue(':postId', $postId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function rowToDTO(array $row, ?string $currentUserId = null): PostDTO {
        $postId = (int)$row['post_id'];
        $tags = $this->postTagRepository->findTagsByPost($postId);
        $course = $this->courseRepository->findById((int)$row['course_id']);
        $category = $this->categoryRepository->findById((int)$row['category_id']);
        $likes = $this->countLikes($postId);
        $dislikes = $this->countDislikes($postId);
        $attachments = $this->attachmentRepository->findByPostId($postId);
        // Ottieni la reazione dell'utente corrente (null, true=like, false=dislike)
        $likedByCurrentUser = null;
        if ($currentUserId !== null) {
            $likedByCurrentUser = $this->hasUserVoted($postId, $currentUserId);
        }
        $dto = new PostDTO(
            postId: $postId,
            author: $row['author'],
            title: $row['title'],
            description: $row['description'],
            createdAt: $row['created_at'],
            course: $course,
            tags: $tags,
            category: $category,
            likes: $likes,
            dislikes: $dislikes,
            likedByUser: $likedByCurrentUser,
            attachments: $attachments
        );
        return $dto;
    }
}



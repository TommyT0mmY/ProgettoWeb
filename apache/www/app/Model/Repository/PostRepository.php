<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\PostDTO;
use Unibostu\Model\DTO\PostFilterDTO;
use Unibostu\Model\DTO\CreateUserPostDTO;

use Unibostu\Core\Database;
use PDO;

class PostRepository {
    private PDO $pdo;
    private PostTagRepository $postTagRepository;
    private PostCategoryRepository $postCategoryRepository;

    public function __construct() {
        $this->pdo = Database::getConnection();
        $this->postTagRepository = new PostTagRepository();
        $this->postCategoryRepository = new PostCategoryRepository();
    }

    /**
     * Recupera un post tramite ID
     */
    public function findById(int $idpost): ?PostDTO {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE idpost = :idpost");
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera post con filtri applicati
     */     
    public function findWithFilters(string $idutente, PostFilterDTO $filter): array {
        $sql = "SELECT DISTINCT p.* FROM posts p";
        $conditions = [];
        $params = [];

        // Join per categorie se filtrate
        if (!empty($filter->categorie)) {
            $sql .= " LEFT JOIN castegorie_posts cp ON p.idpost = cp.idpost";
            $placeholders = array_fill(0, count($filter->categorie), '?');
            $conditions[] = "cp.idcategoria IN (" . implode(',', $placeholders) . ")";
            $params = array_merge($params, $filter->categorie);
        }

        // Join per tag se filtrati
        if (!empty($filter->tags)) {
            $sql .= " LEFT JOIN post_tags pt ON p.idpost = pt.idpost";
            $tagConditions = [];
            foreach ($filter->tags as $tag) {
                $tagConditions[] = "(pt.tipo = ? AND pt.idcorso = ?)";
                $params[] = $tag['tipo'];
                $params[] = $tag['idcorso'];
            }
            if (!empty($tagConditions)) {
                $conditions[] = "(" . implode(" OR ", $tagConditions) . ")";
            }
        }

        // Filtro per corsi
        if (!empty($filter->corso)) {
            $params[] = $filter->corso;
            $conditions[] = " p.idcorso IN (?)";
        } else {
            $params[] = $idutente;
            $conditions[] = " p.idcorso IN (
                SELECT idcorso FROM utenti_corsi WHERE idutente = ?
            )";
        }

        // Aggiungi WHERE clause se ci sono condizioni
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        if ($filter->ordinamento === 'ASC') {
            $sql .= " AND p.idpost > ?";
        } else {
            $sql .= " AND p.idpost < ?";
        }
        $params[] = $filter->lastId;

        // Ordinamento
        $sql .= " ORDER BY p.data_creazione " . $filter->ordinamento;

        // Limit e offset
        $sql .= " LIMIT ?";
        $params[] = $filter->limit;

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
            $posts[] = $this->rowToDTO($row);
        }
        return $posts;
    }

    /**
     * Recupera i post di un utente
     */
    public function findByUserId(string $idutente): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM posts 
             WHERE idutente = :idutente
             ORDER BY data_creazione DESC"
        );
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
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
            $posts[] = $this->rowToDTO($row);
        }
        return $posts;
    }

    /**
     * Salva un nuovo post
     */
    public function save(CreateUserPostDTO $dto): void {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "INSERT INTO posts 
                (titolo, descrizione, percorso_allegato, data_creazione, idutente, idcorso)
                VALUES (:titolo, :descrizione, :percorso_allegato, :data_creazione, :idutente, :idcorso)"
            );
            $stmt->bindValue(':titolo', $dto->titolo, PDO::PARAM_STR);
            $stmt->bindValue(':descrizione', $dto->descrizione, PDO::PARAM_STR);
            $stmt->bindValue(':percorso_allegato', $dto->percorso_allegato ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':data_creazione', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->bindValue(':idutente', $dto->idutente, PDO::PARAM_STR);
            $stmt->bindValue(':idcorso', $dto->idcorso ?? null, PDO::PARAM_INT);
            $stmt->execute();
            $idpost = (int)$this->pdo->lastInsertId();

            // Salva i tag
            if (!empty($dto->tags)) {
                foreach ($dto->tags as $tag) {
                    $this->postTagRepository->addTagToPost($idpost, $tag['tipo'], $tag['idcorso']);
                }
            }

            // Salva le categorie
            if (!empty($dto->categorie)) {
                foreach ($dto->categorie as $idcategoria) {
                    $this->postCategoryRepository->addCategoryToPost($idpost, $idcategoria);
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
     * @param int $idpost ID del post
     * @param string $idutente ID dell'utente
     * @param bool $isLike true per like, false per dislike
     */
    public function addVote(int $idpost, string $idutente, bool $isLike): bool {
        // Verifica se l'utente ha già votato
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM likes WHERE idpost = :idpost AND idutente = :idutente"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ((int)$result['count'] > 0) {
            throw new \Exception("Hai già votato questo post");
        }
        
        // Inserisci il voto nella tabella likes
        $stmtVote = $this->pdo->prepare(
            "INSERT INTO likes (idpost, idutente, is_like) VALUES (:idpost, :idutente, :is_like)"
        );
        $stmtVote->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmtVote->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        $stmtVote->bindValue(':is_like', $isLike ? 1 : 0, PDO::PARAM_INT);
        
        if (!$stmtVote->execute()) {
            throw new \Exception("Errore durante il salvataggio del voto");
        }
        
        return true;
    }

    /**
     * Rimuove un voto (like/dislike) da parte di un utente da un post
     * Usa la tabella likes per tracciare chi ha tolto il voto
     * 
     * @param int $idpost ID del post
     * @param string $idutente ID dell'utente
     */
    public function removeVote(int $idpost, string $idutente): bool {
        // Verifica se l'utente ha messo voto
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM likes WHERE idpost = :idpost AND idutente = :idutente"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ((int)$result['count'] === 0) {
            throw new \Exception("Non hai votato questo post");
        }
        
        // Rimuovi il voto dalla tabella likes
        $stmtDelete = $this->pdo->prepare(
            "DELETE FROM likes WHERE idpost = :idpost AND idutente = :idutente"
        );
        $stmtDelete->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmtDelete->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        
        if (!$stmtDelete->execute()) {
            throw new \Exception("Errore durante la rimozione del voto");
        }
        
        return true;
    }

    /**
     * Conta i like di un post
     */
    public function countLikes(int $idpost): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE idpost = :idpost AND is_like = 1");
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Conta i dislike di un post
     */
    public function countDislikes(int $idpost): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE idpost = :idpost AND is_like = 0");
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    /**
     * Verifica che l'utente ha messo like o dislike
     */
    private function hasUserVoted(int $idpost, string $idutente): ?bool {
        $stmt = $this->pdo->prepare(
            "SELECT is_like FROM likes WHERE idpost = :idpost AND idutente = :idutente"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
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
    public function delete(int $idpost): bool {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE idpost = :idpost");
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verifica che l'utente ha messo like o dislike
     */

    private function rowToDTO(array $row): PostDTO {
        $idpost = (int)$row['idpost'];
        $tags = $this->postTagRepository->findTagsByPost($idpost);
        $categorie = array_map(fn($cat) => $cat['idcategoria'], $this->postCategoryRepository->findCategoriesByPost($idpost));
        $likes = $this->countLikes($idpost);
        $dislikes = $this->countDislikes($idpost);
        $likedByCurrentUser = $this->hasUserVoted($idpost, $row['idutente']);

        $dto = new PostDTO(
            $idpost,
            $row['titolo'],
            $row['descrizione'],
            $row['percorso_allegato'],
            $row['data_creazione'],
            (string)$row['idutente'],
            $row['idcorso'],
            $tags,
            $categorie,
            $likes,
            $dislikes,
            $likedByCurrentUser
        );
        return $dto;
    }
}



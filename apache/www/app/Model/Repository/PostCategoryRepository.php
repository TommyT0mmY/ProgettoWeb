<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Core\Database;
use PDO;

class PostCategoryRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera tutte le categorie di un post
     */
    public function findCategoriesByPost(int $idpost): array {
        $stmt = $this->pdo->prepare(
            "SELECT idcategoria FROM categorie_posts WHERE idpost = :idpost"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupera i post di una categoria
     */
    public function findPostsByCategory(int $idcategoria): array {
        $stmt = $this->pdo->prepare(
            "SELECT idpost FROM categorie_posts WHERE idcategoria = :idcategoria"
        );
        $stmt->bindValue(':idcategoria', $idcategoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aggiunge una categoria a un post
     */
    public function addCategoryToPost(int $idpost, int $idcategoria): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categorie_posts (idpost, idcategoria)
             VALUES (:idpost, :idcategoria)"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idcategoria', $idcategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Rimuove una categoria da un post
     */
    public function removeCategoryFromPost(int $idpost, int $idcategoria): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM categorie_posts WHERE idpost = :idpost AND idcategoria = :idcategoria"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        $stmt->bindValue(':idcategoria', $idcategoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Rimuove tutte le categorie da un post
     */
    public function removeAllCategoriesFromPost(int $idpost): bool {
        $stmt = $this->pdo->prepare(
            "DELETE FROM categorie_posts WHERE idpost = :idpost"
        );
        $stmt->bindValue(':idpost', $idpost, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

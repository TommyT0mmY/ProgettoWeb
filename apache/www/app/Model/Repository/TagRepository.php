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
    public function findByIdAndCourse(int $idtag, int $idcorso): ?TagDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE idtag = :idtag AND idcorso = :idcorso"
        );
        $stmt->bindValue(':idtag', $idtag, PDO::PARAM_INT);
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera un tag tramite tipo e corso
     */
    public function findByTypeAndCourse(string $tipo, int $idcorso): ?TagDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE tipo = :tipo AND idcorso = :idcorso"
        );
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera tutti i tag di un corso
     */
    public function findByCourse(int $idcorso): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM tags WHERE idcorso = :idcorso ORDER BY tipo"
        );
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Salva un nuovo tag
     * @throws \Exception in caso di errore
     */
    public function save(string $tipo, int $idcorso): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tags (tipo, idcorso)
             VALUES (:tipo, :idcorso)"
        );
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':idcorso', $idcorso, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante il salvataggio del tag");
        }
    }

    /**
     * Elimina un tag
     * @throws \Exception in caso di errore
     */
    public function delete(int $idtag): void {
        $stmt = $this->pdo->prepare(
            "DELETE FROM tags WHERE idtag = :idtag"
        );
        $stmt->bindValue(':idtag', $idtag, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'eliminazione del tag");
        }
    }

    private function rowToDTO(array $row): TagDTO {
        return new TagDTO(
            (int)$row['idtag'],
            $row['tipo'],
            (int)$row['idcorso']
        );
    }
}

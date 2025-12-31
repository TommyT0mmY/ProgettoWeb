<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\Entity\AdminEntity;
use Unibostu\Core\Database;
use PDO;

class AdminRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera un amministratore tramite ID
     */
    public function findByAdminId(string $idamministratore): ?AdminEntity {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM amministratori WHERE idamministratore = :idamministratore"
        );
        $stmt->bindValue(':idamministratore', $idamministratore, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToEntity($row) : null;
    }

    /**
     * Recupera un amministratore tramite identita (tabella intermedia entita)
     * Usato quando si ha solo identita
     */
    public function findByEntita(int $identita): ?AdminEntity {
        $stmt = $this->pdo->prepare(
            "SELECT a.* 
             FROM amministratori a, entita e 
             WHERE a.identita = e.identita 
              AND e.identita = :identita"
        );
        $stmt->bindValue(':identita', $identita, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToEntity($row) : null;
    }

    /**
     * Salva un nuovo amministratore
     * 1. Inserisce prima un record in entita
     * 2. Recupera l'identita generato
     * 3. Inserisce il record in amministratori con l'identita appena creato
     */
    public function save(AdminEntity $admin): bool {
        try {
            $this->pdo->beginTransaction();

            // Step 1: Inserisci in entita per generare un nuovo identita
            $stmtEntita = $this->pdo->prepare(
                "INSERT INTO entita DEFAULT VALUES"
            );
            $stmtEntita->execute();

            // Step 2: Recupera l'identita appena creato (il più grande)
            $stmtLastId = $this->pdo->prepare(
                "SELECT MAX(identita) as identita FROM entita"
            );
            $stmtLastId->execute();
            $result = $stmtLastId->fetch(PDO::FETCH_ASSOC);
            $newIdentita = (int)$result['identita'];

            // Step 3: Inserisci in amministratori con il nuovo identita
            $stmtAdmin = $this->pdo->prepare(
                "INSERT INTO amministratori (idamministratore, identita, password)
                 VALUES (:idamministratore, :identita, :password)"
            );
            $stmtAdmin->bindValue(':idamministratore', $admin->idamministratore, PDO::PARAM_STR);
            $stmtAdmin->bindValue(':identita', $newIdentita, PDO::PARAM_INT);
            $stmtAdmin->bindValue(':password', $admin->password, PDO::PARAM_STR);
            $success = $stmtAdmin->execute();

            $this->pdo->commit();
            return $success;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Aggiorna i dati di un amministratore
     */
    public function update(AdminEntity $admin): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE amministratori 
             SET password = :password
             WHERE idamministratore = :idamministratore"
        );
        $stmt->bindValue(':password', $admin->password, PDO::PARAM_STR);
        $stmt->bindValue(':idamministratore', $admin->idamministratore, PDO::PARAM_STR);
        return $stmt->execute();
    }

    private function rowToEntity(array $row): AdminEntity {
        return new AdminEntity(
            $row['idamministratore'],
            (int)$row['identita'],
            $row['password']
        );
    }
}
<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\AdminDTO;
use Unibostu\Model\DTO\CreateAdminDTO;
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
    public function findByAdminId(string $idamministratore): ?AdminDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM amministratori WHERE idamministratore = :idamministratore"
        );
        $stmt->bindValue(':idamministratore', $idamministratore, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    private function rowToDTO(array $row): AdminDTO {
        return new AdminDTO(
            $row['idamministratore'],
            $row['password']
        );
    }
}
<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\AdminDTO;
use PDO;

class AdminRepository extends BaseRepository {

    /**
     * Retrieves an administrator by ID
     */
    public function findByAdminId(string $adminId): ?AdminDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM administrators WHERE admin_id = :adminId"
        );
        $stmt->bindValue(':adminId', $adminId, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Verifies if the admin exists 
     */
    public function exists(string $adminId): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM administrators WHERE admin_id = :adminId"
        );
        $stmt->bindValue(':adminId', $adminId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }

    protected function rowToDTO(array $row): AdminDTO {
        return new AdminDTO(
            $row['admin_id'],
            $row['password']
        );
    }
}

<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\AdminRepository;

class AdminService {
    private AdminRepository $adminRepository;

    public function __construct() {
        $this->adminRepository = new AdminRepository();
    }

    /**
     * Verifies admin credentials
     *
     * @return bool true if credentials are valid, false otherwise
     */
    public function checkCredentials(string $adminId, string $password): bool {
        $admin = $this->adminRepository->findByAdminId($adminId);
        if (!$admin) {
            return false;
        }
        if (!password_verify($password, $admin->password)) {
            return false;
        }
        return true;
    }

    public function adminExists(string $adminId): bool {
        return $this->adminRepository->adminExists($adminId);
    }
}


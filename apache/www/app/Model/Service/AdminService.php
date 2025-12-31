<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\AdminRepository;
use Unibostu\Model\DTO\AdminDTO;
use Unibostu\Model\Entity\AdminEntity;

class AdminService {
    private AdminRepository $adminRepository;

    public function __construct() {
        $this->adminRepository = new AdminRepository();
    }

    /**
     * Ottiene il profilo di un amministratore tramite ID
     */
    public function getAdminProfile(string $idamministratore): ?AdminDTO {
        $admin = $this->adminRepository->findByAdminId($idamministratore);
        return $admin ? new AdminDTO($admin) : null;
    }

    /**
     * Verifica le credenziali di un amministratore
     */
    public function authenticate(string $idamministratore, string $password): ?AdminDTO {
        $admin = $this->adminRepository->findByAdminId($idamministratore);
        
        if ($admin && password_verify($password, $admin->password)) {
            return new AdminDTO($admin);
        }

        return null;
    }

    /**
     * Registra un nuovo amministratore
     */
    public function registerAdmin(
        string $idamministratore,
        string $password
    ): bool {
        $admin = new AdminEntity(
            $idamministratore,
            0,
            password_hash($password, PASSWORD_BCRYPT)
        );
        return $this->adminRepository->save($admin);
    }

    /**
     * Aggiorna la password di un amministratore
     */
    public function updateProfile(string $idamministratore, string $password): bool {
        $admin = $this->adminRepository->findByAdminId($idamministratore);
        if (!$admin) {
            return false;
        }

        $admin->password = password_hash($password, PASSWORD_BCRYPT);
        return $this->adminRepository->update($admin);
    }
}


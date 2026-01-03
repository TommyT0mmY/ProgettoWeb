<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\AdminRepository;
use Unibostu\Model\DTO\AdminDTO;

class AdminService {
    private AdminRepository $adminRepository;

    public function __construct() {
        $this->adminRepository = new AdminRepository();
    }

    /**
     * Verifica le credenziali di un amministratore
     * @throws \Exception se le credenziali non sono valide
     */
    public function authenticate(string $idamministratore, string $password): void {
        $admin = $this->adminRepository->findByAdminId($idamministratore);
        
        if (!$admin) {
            throw new \Exception("Amministratore non trovato");
        }

        if (!password_verify($password, $admin->password)) {
            throw new \Exception("Password errata");
        }
    }
}


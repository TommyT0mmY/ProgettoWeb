<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Model\Service\AdminService;

class AdminController {
    private AdminService $adminService;

    public function __construct() {
        $this->adminService = new AdminService();
    }

    /**
     * Carica il profilo di un amministratore
     */
    public function loadProfile(string $idamministratore): void {
        $profileDTO = $this->adminService->getAdminProfile($idamministratore);
        // Passa i dati alla view
        // $this->view('admin/profile', ['profileDTO' => $profileDTO]);
    }

    /**
     * Autentica un amministratore
     */
    public function authenticate(string $idamministratore, string $password): void {
        $profileDTO = $this->adminService->authenticate($idamministratore, $password);
        if ($profileDTO) {
            // Login riuscito
            // $_SESSION['admin'] = $profileDTO;
        } else {
            // Login fallito
        }
    }

    /**
     * Registra un nuovo amministratore
     */
    public function register(string $idamministratore, string $password): void {
        $result = $this->adminService->registerAdmin($idamministratore, $password);
        // Gestisci il risultato
    }

    /**
     * Aggiorna il profilo di un amministratore
     */
    public function updateProfile(string $idamministratore, string $password): void {
        $result = $this->adminService->updateProfile($idamministratore, $password);
        // Gestisci il risultato
    }
}

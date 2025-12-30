<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Model\Service\UserService;

class UsersController {
    private UserService $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    /**
     * Carica il profilo di un utente
     */
    public function loadProfile(string $idutente): void {
        $profileDTO = $this->userService->getUserProfile($idutente);
        // Passa i dati alla view
        // $this->view('user/profile', ['profileDTO' => $profileDTO]);
    }

    /**
     * Autentica un utente
     */
    public function authenticate(string $idutente, string $password): void {
        $profileDTO = $this->userService->authenticate($idutente, $password);
        if ($profileDTO) {
            // Login riuscito
            // $_SESSION['user'] = $profileDTO;
        } else {
            // Login fallito
        }
    }

    /**
     * Registra un nuovo utente
     */
    public function register(string $idutente, int $identita, string $password, string $nome, string $cognome, int $idfacolta): void {
        $result = $this->userService->registerUser($idutente, $identita, $password, $nome, $cognome, $idfacolta);
        // Gestisci il risultato
    }

    /**
     * Aggiorna il profilo di un utente
     */
    public function updateProfile(string $idutente, string $nome, string $cognome): void {
        $result = $this->userService->updateProfile($idutente, $nome, $cognome);
        // Gestisci il risultato
    }
}

?>

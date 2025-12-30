<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\UserProfileDTO;
use Unibostu\Model\Entity\UserEntity;

class UserService {
    private UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    /**
     * Ottiene il profilo di un utente tramite ID
     */
    public function getUserProfile(string $idutente): ?UserProfileDTO {
        $user = $this->userRepository->findByUserId($idutente);
        return $user ? new UserProfileDTO($user) : null;
    }

    /**
     * Verifica le credenziali di un utente
     */
    public function authenticate(string $idutente, string $password): ?UserProfileDTO {
        $user = $this->userRepository->findByUserId($idutente);
        
        if ($user && password_verify($password, $user->password) && !$user->utente_sospeso) {
            return new UserProfileDTO($user);
        }

        return null;
    }

    /**
     * Registra un nuovo utente
     */
    public function registerUser(
        string $idutente,
        string $password,
        string $nome,
        string $cognome,
        int $idfacolta
    ): bool {
        $user = new UserEntity(
            $idutente,
            0,
            password_hash($password, PASSWORD_BCRYPT),
            $nome,
            $cognome,
            $idfacolta,
            false
        );

        return $this->userRepository->save($user);
    }

    /**
     * Aggiorna il profilo di un utente
     */
    public function updateProfile(string $idutente, string $nome, string $cognome): bool {
        $user = $this->userRepository->findByUserId($idutente);
        if (!$user) {
            return false;
        }

        $user->nome = $nome;
        $user->cognome = $cognome;

        return $this->userRepository->update($user);
    }
}

?>
<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\UserDTO;

class UserService {
    private UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    /**
     * Ottiene il profilo di un utente tramite ID
     */
    public function getUserProfile(string $userId): ?UserDTO {
        return $this->userRepository->findByUserId($userId);
    }

    /**
     * Verifica le credenziali di un utente
     * @throws \Exception se le credenziali non sono valide
     */
    public function authenticate(string $userId, string $password): bool {
        $user = $this->userRepository->findByUserId($userId);
        
        if (!$user) {
            throw new \Exception("Utente non trovato");
        }

        if (!password_verify($password, $user->password)) {
            throw new \Exception("Password errata");
        }

        if ($user->suspended) {
            throw new \Exception("Utente sospeso, accesso negato");
        }

        return true;
    }

    /**
     * Registra un nuovo utente
     * @throws \Exception se l'username è già preso o i dati sono invalidi
     */
    public function registerUser(UserDTO $dto): void {
        // Verifica che l'username non esista già
        $existingUser = $this->userRepository->findByUserId($dto->userId);
        if ($existingUser) {
            throw new \Exception("Username '$dto->userId' già utilizzato");
        }

        if (empty($dto->userId)) {
            throw new \Exception("L'username non può essere vuoto");
        }

        if ($dto->facultyId <= 0) {
            throw new \Exception("Facoltà non valida");
        }

        if (empty($dto->firstName) || empty($dto->lastName)) {
            throw new \Exception("Nome e cognome non possono essere vuoti");
        }

        if (empty($dto->password)) {
            throw new \Exception("La password non può essere vuota");
        }

        $this->userRepository->save($dto);
    }

    /**
     * Aggiorna il profilo di un utente
     * @throws \Exception se l'utente non esiste o i dati non sono validi
     */
    public function updateProfile(UserDTO $dto): void {
        $user = $this->userRepository->findByUserId($dto->userId);
        if (!$user) {
            throw new \Exception("Utente '$dto->userId' non trovato");
        }

        if (empty($dto->firstName) || empty($dto->lastName)) {
            throw new \Exception("Nome e cognome non possono essere vuoti");
        }

        if (empty($dto->password)) {
            throw new \Exception("La password non può essere vuota");
        }

        $this->userRepository->updateProfile($dto);
    }

    /**
     * Sospende un utente
     * @throws \Exception se l'utente non esiste
     */
    public function suspendUser(string $userId): void {
        $user = $this->userRepository->findByUserId($userId);
        if (!$user) {
            throw new \Exception("Utente '$userId' non trovato");
        }

        $this->userRepository->suspendUser($userId);
    }
}
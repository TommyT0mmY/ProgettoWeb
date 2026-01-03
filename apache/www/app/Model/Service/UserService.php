<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\PublicUserDTO;
use Unibostu\Model\DTO\CreateUserDTO;
use Unibostu\Model\DTO\UpdateUserDTO;

class UserService {
    private UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    /**
     * Ottiene il profilo di un utente tramite ID
     */
    public function getUserProfile(string $idutente): ?PublicUserDTO {
        return $this->userRepository->findByUserIdPublic($idutente);
    }

    /**
     * Verifica le credenziali di un utente
     * @throws \Exception se le credenziali non sono valide
     */
    public function authenticate(string $idutente, string $password): bool {
        $user = $this->userRepository->findByUserIdPrivate($idutente);
        
        if (!$user) {
            throw new \Exception("Utente non trovato");
        }

        if (!password_verify($password, $user->password)) {
            throw new \Exception("Password errata");
        }

        if ($user->utente_sospeso) {
            throw new \Exception("Utente sospeso, accesso negato");
        }

        return true;
    }

    /**
     * Registra un nuovo utente
     * @throws \Exception se l'username è già preso o i dati sono invalidi
     */
    public function registerUser(CreateUserDTO $dto): void {
        // Verifica che l'username non esista già
        $existingUser = $this->userRepository->findByUserIdPrivate($dto->idutente);
        if ($existingUser) {
            throw new \Exception("Username '$dto->idutente' già utilizzato");
        }

        if ($dto->idfacolta <= 0) {
            throw new \Exception("Facoltà non valida");
        }

        if (empty($dto->nome) || empty($dto->cognome)) {
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
    public function updateProfile(UpdateUserDTO $dto): void {
        $user = $this->userRepository->findByUserIdPrivate($dto->idutente);
        if (!$user) {
            throw new \Exception("Utente '$dto->idutente' non trovato");
        }

        if ($dto->nuovo_idutente !== $dto->idutente) {
            $existingUser = $this->userRepository->findByUserIdPrivate($dto->nuovo_idutente);
            if ($existingUser) {
                throw new \Exception("Username '{$dto->nuovo_idutente}' già utilizzato");
            }
        }

        if (empty($nome) || empty($cognome)) {
            throw new \Exception("Nome e cognome non possono essere vuoti");
        }

        if (empty($password)) {
            throw new \Exception("La password non può essere vuota");
        }

        $this->userRepository->updateProfile($dto);
    }

    /**
     * Sospende un utente
     * @throws \Exception se l'utente non esiste
     */
    public function suspendUser(string $idutente): void {
        $user = $this->userRepository->findByUserIdPublic($idutente);
        if (!$user) {
            throw new \Exception("Utente '$idutente' non trovato");
        }

        $this->userRepository->suspendUser($idutente);
    }
}
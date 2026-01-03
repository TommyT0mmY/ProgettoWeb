<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\PublicUserDTO;
use Unibostu\Core\Database;
use Unibostu\Model\DTO\PrivateUserDto;
use PDO;

class UserRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera un utente tramite ID utente
     */
    public function findByUserIdPublic(string $idutente): ?PublicUserDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM utenti WHERE idutente = :idutente"
        );
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Recupera un utente tramite ID utente (privato)
     */
    public function findByUserIdPrivate(string $idutente): ?PrivateUserDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM utenti WHERE idutente = :idutente"
        );
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToPrivateDTO($row) : null;
    }

    /**
     * Verifica se un utente esiste per idutente
     */
    public function isUser(string $idutente): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM utenti WHERE idutente = :idutente"
        );
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }

    /**
     * Salva un nuovo utente
     * @throws \Exception in caso di errore nel salvataggio
     */
    public function save(\Unibostu\Model\DTO\CreateUserDTO $dto): void {
        $stmtUtenti = $this->pdo->prepare(
            "INSERT INTO utenti 
                (idutente, password, nome, cognome, idfacolta, utente_sospeso)
                VALUES (:idutente, :password, :nome, :cognome, :idfacolta, :utente_sospeso)"
        );
        $stmtUtenti->bindValue(':idutente', $dto->idutente, PDO::PARAM_STR);
        $stmtUtenti->bindValue(':password', password_hash($dto->password, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $stmtUtenti->bindValue(':nome', $dto->nome, PDO::PARAM_STR);
        $stmtUtenti->bindValue(':cognome', $dto->cognome, PDO::PARAM_STR);
        $stmtUtenti->bindValue(':idfacolta', $dto->idfacolta, PDO::PARAM_INT);
        $stmtUtenti->bindValue(':utente_sospeso', false, PDO::PARAM_BOOL);
        $success = $stmtUtenti->execute();

        if (!$success) {
            throw new \Exception("Errore durante il salvataggio dell'utente");
        }
    }

    /**
     * Aggiorna il profilo di un utente
     * @throws \Exception in caso di errore
     */
    public function updateProfile(\Unibostu\Model\DTO\UpdateUserDTO $dto): void {
        $stmt = $this->pdo->prepare(
            "UPDATE utenti 
             SET nome = :nome, cognome = :cognome, password = :password, idutente = :nuovo_idutente
             WHERE idutente = :idutente"
        );
        $stmt->bindValue(':nome', $dto->nome, PDO::PARAM_STR);
        $stmt->bindValue(':cognome', $dto->cognome, PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($dto->password, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $stmt->bindValue(':nuovo_idutente', $dto->nuovo_idutente, PDO::PARAM_STR);
        $stmt->bindValue(':idutente', $dto->idutente, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception("Errore durante l'aggiornamento del profilo");
        }
    }

    /**
     * Sospende un utente
     * @throws \Exception in caso di errore
     */
    public function suspendUser(string $idutente): void {
        $stmt = $this->pdo->prepare(
            "UPDATE utenti 
             SET utente_sospeso = true
             WHERE idutente = :idutente"
        );
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            throw new \Exception("Errore durante la sospensione dell'utente");
        }
    }

    private function rowToDTO(array $row): PublicUserDTO {
        return new PublicUserDTO(
            $row['idutente'],
            $row['nome'],
            $row['cognome'],
            (int)$row['idfacolta'],
            (bool)$row['utente_sospeso'],
        );
    }

    private function rowToPrivateDTO(array $row): PrivateUserDTO {
        return new PrivateUserDTO(
            $row['idutente'],
            $row['nome'],
            $row['cognome'],
            (int)$row['idfacolta'],
            (bool)$row['utente_sospeso'],
            $row['password']
        );
    }
}
<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\Entity\UserEntity;
use Unibostu\Core\Database;
use PDO;

class UserRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera un utente tramite ID utente (contiene anche identita)
     */
    public function findByUserId(string $idutente): ?UserEntity {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM utenti WHERE idutente = :idutente"
        );
        $stmt->bindValue(':idutente', $idutente, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToEntity($row) : null;
    }

    /**
     * Recupera un utente tramite identita (tabella intermedia entita)
     * Usato quando si ha solo identita da commenti/posts
     */
    public function findByEntita(int $identita): ?UserEntity {
        $stmt = $this->pdo->prepare(
            "SELECT u.* 
             FROM utenti u, entita e 
             WHERE u.identita = e.identita 
              AND e.identita = :identita"
        );
        $stmt->bindValue(':identita', $identita, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToEntity($row) : null;
    }

    /**
     * Salva un nuovo utente
     * 1. Inserisce prima un record in entita
     * 2. Recupera l'identita generato
     * 3. Inserisce il record in utenti con l'identita appena creato
     */
    public function save(UserEntity $user): bool {
        try {
            $this->pdo->beginTransaction();

            // Step 1: Inserisci in entita per generare un nuovo identita
            $stmtEntita = $this->pdo->prepare(
                "INSERT INTO entita DEFAULT VALUES"
            );
            $stmtEntita->execute();

            // Step 2: Recupera l'identita appena creato (il più grande)
            $stmtLastId = $this->pdo->prepare(
                "SELECT MAX(identita) as identita FROM entita"
            );
            $stmtLastId->execute();
            $result = $stmtLastId->fetch(PDO::FETCH_ASSOC);
            $newIdentita = (int)$result['identita'];

            // Step 3: Inserisci in utenti con il nuovo identita
            $stmtUtenti = $this->pdo->prepare(
                "INSERT INTO utenti 
                 (idutente, identita, password, nome, cognome, idfacolta, utente_sospeso)
                 VALUES (:idutente, :identita, :password, :nome, :cognome, :idfacolta, :utente_sospeso)"
            );
            $stmtUtenti->bindValue(':idutente', $user->idutente, PDO::PARAM_STR);
            $stmtUtenti->bindValue(':identita', $newIdentita, PDO::PARAM_INT);
            $stmtUtenti->bindValue(':password', $user->password, PDO::PARAM_STR);
            $stmtUtenti->bindValue(':nome', $user->nome, PDO::PARAM_STR);
            $stmtUtenti->bindValue(':cognome', $user->cognome, PDO::PARAM_STR);
            $stmtUtenti->bindValue(':idfacolta', $user->idfacolta, PDO::PARAM_INT);
            $stmtUtenti->bindValue(':utente_sospeso', $user->utente_sospeso, PDO::PARAM_BOOL);
            $success = $stmtUtenti->execute();

            $this->pdo->commit();
            return $success;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Aggiorna i dati di un utente
     */
    public function update(UserEntity $user): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE utenti 
             SET password = :password, nome = :nome, cognome = :cognome, idfacolta = :idfacolta, utente_sospeso = :utente_sospeso
             WHERE idutente = :idutente"
        );
        $stmt->bindValue(':password', $user->password, PDO::PARAM_STR);
        $stmt->bindValue(':nome', $user->nome, PDO::PARAM_STR);
        $stmt->bindValue(':cognome', $user->cognome, PDO::PARAM_STR);
        $stmt->bindValue(':idfacolta', $user->idfacolta, PDO::PARAM_INT);
        $stmt->bindValue(':utente_sospeso', $user->utente_sospeso, PDO::PARAM_BOOL);
        $stmt->bindValue(':idutente', $user->idutente, PDO::PARAM_STR);
        return $stmt->execute();
    }

    private function rowToEntity(array $row): UserEntity {
        return new UserEntity(
            $row['idutente'],
            (int)$row['identita'],
            $row['password'],
            $row['nome'],
            $row['cognome'],
            (int)$row['idfacolta'],
            (bool)$row['utente_sospeso']
        );
    }
}

?>
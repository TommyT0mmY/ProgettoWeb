<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\UserDTO;
use Unibostu\Core\Database;
use PDO;

class UserRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    /**
     * Recupera un utente tramite ID utente
     */
    public function findByUserId(string $userId): ?UserDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE user_id = :userId"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToPrivateDTO($row) : null;
    }

    /**
     * Verifies if the user exists 
     *
     * @return bool True if the user exists, false otherwise
     */
    public function userExists(string $userId): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM users WHERE user_id = :userId"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }

    /**
     * Registers a new user.
     *
     * @throws \RuntimeException in case of error
     */
    public function register(UserDTO $dto): void {
        $stmtUtenti = $this->pdo->prepare(
            "INSERT INTO users 
                (user_id, password, first_name, last_name, faculty_id, suspended)
                VALUES (:userId, :password, :firstName, :lastName, :facultyId, :suspended)"
        );
        $stmtUtenti->bindValue(':userId', $dto->userId, PDO::PARAM_STR);
        $stmtUtenti->bindValue(':password', password_hash($dto->password, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $stmtUtenti->bindValue(':firstName', $dto->firstName, PDO::PARAM_STR);
        $stmtUtenti->bindValue(':lastName', $dto->lastName, PDO::PARAM_STR);
        $stmtUtenti->bindValue(':facultyId', $dto->facultyId, PDO::PARAM_INT);
        $stmtUtenti->bindValue(':suspended', false, PDO::PARAM_BOOL);
        $success = $stmtUtenti->execute();
        if (!$success) {
            throw new \RuntimeException("Error during user save operation");
        }
    }

    /**
     * Updates user profile information.
     *
     * @throws \RuntimeException in case of error
     */
    // TODO CONTROLLARE MEGLIO QUESTO METODO
    public function updateProfile(UserDTO $dto): void {
        $stmt = $this->pdo->prepare(
            "UPDATE users 
             SET first_name = :firstName, last_name = :lastName, password = :password
             WHERE user_id = :userId"
        );
        $stmt->bindValue(':firstName', $dto->firstName, PDO::PARAM_STR);
        $stmt->bindValue(':lastName', $dto->lastName, PDO::PARAM_STR);
        $stmt->bindValue(':password', password_hash($dto->password, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $stmt->bindValue(':userId', $dto->userId, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            throw new \RuntimeException("Error during user profile update");
        }
    }

    /**
     * Suspends a user account.
     *
     * @throws \RuntimeException in case of error
     */
    public function suspendUser(string $userId): void {
        $stmt = $this->pdo->prepare(
            "UPDATE users 
             SET suspended = true
             WHERE user_id = :userId"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            throw new \RuntimeException("Error during user suspension");
        }
    }

    /**
     * Updates basic user profile information (without password).
     *
     * @throws \RuntimeException in case of error
     */
    public function updateBasicProfile(string $userId, string $firstName, string $lastName, int $facultyId): void {
        $stmt = $this->pdo->prepare(
            "UPDATE users 
             SET first_name = :firstName, last_name = :lastName, faculty_id = :facultyId
             WHERE user_id = :userId"
        );
        $stmt->bindValue(':firstName', $firstName, PDO::PARAM_STR);
        $stmt->bindValue(':lastName', $lastName, PDO::PARAM_STR);
        $stmt->bindValue(':facultyId', $facultyId, PDO::PARAM_INT);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            throw new \RuntimeException("Error during user profile update");
        }
    }

    /**
     * Updates user password.
     *
     * @throws \RuntimeException in case of error
     */
    public function updatePassword(string $userId, string $newPassword): void {
        $stmt = $this->pdo->prepare(
            "UPDATE users 
             SET password = :password
             WHERE user_id = :userId"
        );
        $stmt->bindValue(':password', password_hash($newPassword, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            throw new \RuntimeException("Error during password update");
        }
    }

    private function rowToPrivateDTO(array $row): UserDTO {
        return new UserDTO(
            userId: $row['user_id'],
            firstName: $row['first_name'],
            lastName: $row['last_name'],
            facultyId: (int)$row['faculty_id'],
            password: $row['password'],
            suspended: (bool)$row['suspended'],
        );
    }
}

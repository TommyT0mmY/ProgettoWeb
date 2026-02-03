<?php
declare(strict_types=1);

namespace Unibostu\Model\Repository;

use Unibostu\Model\DTO\UserDTO;
use Unibostu\Core\exceptions\RepositoryException;
use PDO;

class UserRepository extends BaseRepository {
    /**
     * Retrieves all users
     * 
     * @return UserDTO[] Array of UserDTO objects
     * @throws RepositoryException
     */
    public function findAllUsers(): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'rowToDTO'], $rows);
    }

    /**
     * Retrieves a user by user ID
     *
     * @param string $userId The user ID
     * @return UserDTO|null The UserDTO object or null if not found
     * @throws RepositoryException
     */
    public function findByUserId(string $userId): ?UserDTO {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE user_id = :userId"
        );
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->rowToDTO($row) : null;
    }

    /**
     * Verifies if the user exists 
     *
     * @param string $userId The user ID
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
     * @throws RepositoryException if registration fails
     */
    public function register(UserDTO $dto): void {
        $this->executeInTransaction(function() use ($dto) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO users 
                    (user_id, password, first_name, last_name, faculty_id, suspended)
                    VALUES (:userId, :password, :firstName, :lastName, :facultyId, :suspended)"
            );
            $stmt->bindValue(':userId', $dto->userId, PDO::PARAM_STR);
            $stmt->bindValue(':password', password_hash($dto->password, PASSWORD_BCRYPT), PDO::PARAM_STR);
            $stmt->bindValue(':firstName', $dto->firstName, PDO::PARAM_STR);
            $stmt->bindValue(':lastName', $dto->lastName, PDO::PARAM_STR);
            $stmt->bindValue(':facultyId', $dto->facultyId, PDO::PARAM_INT);
            $stmt->bindValue(':suspended', false, PDO::PARAM_BOOL);
            
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to register user");
            }
        });
    }

    /**
     * Updates user profile information.
     *
     * @throws RepositoryException if update fails
     */
    // TODO Review this method more carefully
    public function updateProfile(UserDTO $dto): void {
        $this->executeInTransaction(function() use ($dto) {
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
                throw new RepositoryException("Failed to update user profile");
            }
        });
    }

    /**
     * Suspends a user account.
     *
     * @throws RepositoryException if suspension fails
     */
    public function suspendUser(string $userId): void {
        $this->executeInTransaction(function() use ($userId) {
            $stmt = $this->pdo->prepare(
                "UPDATE users 
                 SET suspended = true
                 WHERE user_id = :userId"
            );
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to suspend user");
            }
        });
    }

    /**
     * Unsuspends a user account.
     *
     * @throws RepositoryException if unsuspension fails
     */
    public function unsuspendUser(string $userId): void {
        $this->executeInTransaction(function() use ($userId) {
            $stmt = $this->pdo->prepare(
                "UPDATE users 
                 SET suspended = false
                 WHERE user_id = :userId"
            );
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to unsuspend user");
            }
        });
    }

    /**
     * Updates basic user profile information (without password).
     *
     * @throws RepositoryException if update fails
     */
    public function updateBasicProfile(string $userId, string $firstName, string $lastName, int $facultyId): void {
        $this->executeInTransaction(function() use ($userId, $firstName, $lastName, $facultyId) {
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
                throw new RepositoryException("Failed to update user profile");
            }
        });
    }

    /**
     * Updates user password.
     *
     * @throws RepositoryException if update fails
     */
    public function updatePassword(string $userId, string $newPassword): void {
        $this->executeInTransaction(function() use ($userId, $newPassword) {
            $stmt = $this->pdo->prepare(
                "UPDATE users 
                 SET password = :password
                 WHERE user_id = :userId"
            );
            $stmt->bindValue(':password', password_hash($newPassword, PASSWORD_BCRYPT), PDO::PARAM_STR);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            
            if (!$stmt->execute()) {
                throw new RepositoryException("Failed to update password");
            }
        });
    }

    protected function rowToDTO(array $row): UserDTO {
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

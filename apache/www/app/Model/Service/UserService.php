<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\UserDTO;

class UserService implements RoleService {
    private UserRepository $userRepository;
    private FacultyService $facultyService;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->facultyService = new FacultyService();
    }

    /**
     * Gets all users.
     *
     * @return UserDTO[] Array of UserDTO objects
     */
    public function getAllUsers(): array {
        return $this->userRepository->findAllUsers();
    }

    /**
     * Retrieves the user profile by user ID.
     *
     * @param string $userId The user ID.
     *
     * @return UserDTO|null The user profile DTO or null if not found.
     */
    public function getUserProfile(string $userId): ?UserDTO {
        return $this->userRepository->findByUserId($userId);
    }

    public function checkCredentials(string $userId, string $password): bool {
        $user = $this->userRepository->findByUserId($userId);
        if (!$user) {
            return false; 
        }
        if (!password_verify($password, $user->password)) {
            return false; 
        }
        if ($user->suspended) {
            return false; 
        }
        return true;
    }

    public function exists(string $userId): bool {
        return $this->userRepository->userExists($userId);
    }

    public function isSuspended(string $userId): bool {
        $user = $this->getUserProfile($userId); 
        if (!$user) {
            return false;
        }
        return $user->suspended;
    }

    /**
     * Registers a new user.
     * If the registration succeeds, no exception is thrown.
     *
     * @throws ValidationException if validation fails
     */
    public function registerUser(UserDTO $dto): void {
        $exceptionBuilder = ValidationException::build();
        $existingUser = $this->getUserProfile($dto->userId);
        if ($existingUser) {
            $exceptionBuilder->addError(ValidationErrorCode::USERNAME_ALREADY_EXISTS);
        }
        if (empty($dto->userId)) {
            $exceptionBuilder->addError(ValidationErrorCode::USERNAME_REQUIRED);
        }
        if (empty($dto->facultyId)) {
            $exceptionBuilder->addError(ValidationErrorCode::FACULTY_REQUIRED);
        }
        if (!$this->facultyService->facultyExists($dto->facultyId)) {
            $exceptionBuilder->addError(ValidationErrorCode::FACULTY_INVALID);
        }
        if (empty($dto->firstName)) {
            $exceptionBuilder->addError(ValidationErrorCode::FIRSTNAME_REQUIRED);
        }
        if (empty($dto->lastName)) {
            $exceptionBuilder->addError(ValidationErrorCode::LASTNAME_REQUIRED);
        }
        if (empty($dto->password)) {
            $exceptionBuilder->addError(ValidationErrorCode::PASSWORD_REQUIRED);
        }
        $exceptionBuilder->throwIfAny();
        $this->userRepository->register($dto);
    }

    /**
     * Updates the profile of an existing user
     *
     * @throws ValidationException if validation fails
     */
    // TODO CONTROLLARE MEGLIO QUESTO METODO
    public function updateProfile(UserDTO $dto): void {
        $exceptionBuilder = ValidationException::build();
        $user = $this->getUserProfile($dto->userId);
        if (!$user) {
            $exceptionBuilder->addError(ValidationErrorCode::USERNAME_REQUIRED);
        }
        if (empty($dto->firstName)) {
            $exceptionBuilder->addError(ValidationErrorCode::FIRSTNAME_REQUIRED);
        }
        if (empty($dto->lastName)) {
            $exceptionBuilder->addError(ValidationErrorCode::LASTNAME_REQUIRED);
        }
        if (empty($dto->password)) {
            $exceptionBuilder->addError(ValidationErrorCode::PASSWORD_REQUIRED);
        }
        $this->userRepository->updateProfile($dto);
    }

    public function suspendUser(string $userId): void {
        $user = $this->getUserProfile($userId);
        if (!$user) {
            throw new ValidationException(errors: [ValidationErrorCode::USERNAME_REQUIRED]);
        }
        $this->userRepository->suspendUser($userId);
    }

    public function unsuspendUser(string $userId): void {
        $user = $this->getUserProfile($userId);
        if (!$user) {
            throw new ValidationException(errors: [ValidationErrorCode::USERNAME_REQUIRED]);
        }
        $this->userRepository->unsuspendUser($userId);
    }

    /**
     * Updates basic profile information (without password) for an existing user.
     *
     * @throws ValidationException if validation fails
     */
    public function updateBasicProfile(string $userId, string $firstName, string $lastName, int $facultyId): void {
        $exceptionBuilder = ValidationException::build();
        
        $user = $this->getUserProfile($userId);
        if (!$user) {
            $exceptionBuilder->addError(ValidationErrorCode::USERNAME_REQUIRED);
        }
        if (empty($firstName)) {
            $exceptionBuilder->addError(ValidationErrorCode::FIRSTNAME_REQUIRED);
        }
        if (empty($lastName)) {
            $exceptionBuilder->addError(ValidationErrorCode::LASTNAME_REQUIRED);
        }
        if (empty($facultyId)) {
            $exceptionBuilder->addError(ValidationErrorCode::FACULTY_REQUIRED);
        }
        if (!$this->facultyService->facultyExists($facultyId)) {
            $exceptionBuilder->addError(ValidationErrorCode::FACULTY_INVALID);
        }
        
        $exceptionBuilder->throwIfAny();
        $this->userRepository->updateBasicProfile($userId, $firstName, $lastName, $facultyId);
    }

    /**
     * Updates user password after verifying the current password.
     *
     * @throws ValidationException if validation fails or current password is incorrect
     */
    public function updatePassword(string $userId, string $currentPassword, string $newPassword): void {
        $exceptionBuilder = ValidationException::build();
        
        $user = $this->getUserProfile($userId);
        if (!$user) {
            $exceptionBuilder->addError(ValidationErrorCode::USERNAME_REQUIRED);
            $exceptionBuilder->throwIfAny();
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $user->password)) {
            $exceptionBuilder->addError(ValidationErrorCode::PASSWORD_INVALID);
        }
        
        if (empty($newPassword)) {
            $exceptionBuilder->addError(ValidationErrorCode::PASSWORD_REQUIRED);
        }
        
        $exceptionBuilder->throwIfAny();
        $this->userRepository->updatePassword($userId, $newPassword);
    }
}

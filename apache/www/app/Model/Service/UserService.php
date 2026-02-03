<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Model\Repository\UserRepository;
use Unibostu\Model\DTO\UserDTO;

class UserService implements RoleService {
    public const MIN_PASSWORD_LENGTH = 6;

    private UserRepository $userRepository;
    private FacultyService $facultyService;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->facultyService = new FacultyService();
    }

    /**
     * Gets all users.
     *
     * @return UserDTO[] All users.
     */
    public function getAllUsers(): array {
        return $this->userRepository->findAllUsers();
    }

    /**
     * Gets a user profile.
     *
     * @param string $userId User ID.
     * @return UserDTO|null User profile or null if not found.
     */
    public function getUserProfile(string $userId): ?UserDTO {
        return $this->userRepository->findByUserId($userId);
    }

    /**
     * Validates user credentials.
     *
     * @param string $userId User ID.
     * @param string $password Plain text password.
     * @return bool True if valid and not suspended.
     */
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

    /**
     * Checks if a user exists.
     *
     * @param string $userId User ID.
     * @return bool True if user exists.
     */
    public function exists(string $userId): bool {
        return $this->userRepository->userExists($userId);
    }

    /**
     * Checks if a user is suspended.
     *
     * @param string $userId User ID.
     * @return bool True if user is suspended.
     */
    public function isSuspended(string $userId): bool {
        $user = $this->getUserProfile($userId); 
        if (!$user) {
            return false;
        }
        return $user->suspended;
    }

    /**
     * Registers a new user.
     *
     * @param UserDTO $dto User data with password.
     * @throws ValidationException When username already exists.
     * @throws ValidationException When username is empty.
     * @throws ValidationException When faculty ID is empty.
     * @throws ValidationException When faculty does not exist.
     * @throws ValidationException When first name is empty.
     * @throws ValidationException When last name is empty.
     * @throws ValidationException When password is empty.
     * @throws ValidationException When password is too short.
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
        } elseif (strlen($dto->password) < self::MIN_PASSWORD_LENGTH) {
            $exceptionBuilder->addError(ValidationErrorCode::PASSWORD_TOO_SHORT);
        }
        $exceptionBuilder->throwIfAny();
        $this->userRepository->register($dto);
    }

    /**
     * Updates a user profile.
     *
     * @param UserDTO $dto Updated user data.
     * @throws ValidationException When user does not exist.
     * @throws ValidationException When first name is empty.
     * @throws ValidationException When last name is empty.
     * @throws ValidationException When password is empty.
     */
    // TODO Review this method more carefully
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

    /**
     * Suspends a user.
     *
     * @param string $userId User ID.
     * @throws ValidationException When user does not exist.
     */
    public function suspendUser(string $userId): void {
        $user = $this->getUserProfile($userId);
        if (!$user) {
            ValidationException::build()->addError(ValidationErrorCode::USERNAME_REQUIRED)->throwIfAny();
        }
        $this->userRepository->suspendUser($userId);
    }

    /**
     * Unsuspends a user.
     *
     * @param string $userId User ID.
     * @throws ValidationException When user does not exist.
     */
    public function unsuspendUser(string $userId): void {
        $user = $this->getUserProfile($userId);
        if (!$user) {
            ValidationException::build()->addError(ValidationErrorCode::USERNAME_REQUIRED)->throwIfAny();
        }
        $this->userRepository->unsuspendUser($userId);
    }

    /**
     * Updates basic profile information without password.
     *
     * @param string $userId User ID.
     * @param string $firstName New first name.
     * @param string $lastName New last name.
     * @param int $facultyId New faculty ID.
     * @throws ValidationException When user does not exist.
     * @throws ValidationException When first name is empty.
     * @throws ValidationException When last name is empty.
     * @throws ValidationException When faculty ID is empty.
     * @throws ValidationException When faculty does not exist.
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
     * Updates user password after verifying current password.
     *
     * @param string $userId User ID.
     * @param string $currentPassword Current password.
     * @param string $newPassword New password.
     * @throws ValidationException When user does not exist.
     * @throws ValidationException When current password is incorrect.
     * @throws ValidationException When new password is empty.
     * @throws ValidationException When new password is too short.
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
        } elseif (strlen($newPassword) < self::MIN_PASSWORD_LENGTH) {
            $exceptionBuilder->addError(ValidationErrorCode::PASSWORD_TOO_SHORT);
        }
        $exceptionBuilder->throwIfAny();
        $this->userRepository->updatePassword($userId, $newPassword);
    }
}

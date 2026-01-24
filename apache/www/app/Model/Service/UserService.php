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
            ValidationException::build()->addError(ValidationErrorCode::USERNAME_REQUIRED)->throwIfAny();
        }
        $this->userRepository->suspendUser($userId);
    }
}

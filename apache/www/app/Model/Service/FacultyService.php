<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Model\Repository\FacultyRepository;
use Unibostu\Model\DTO\FacultyDTO;

class FacultyService {
    private FacultyRepository $facultyRepository;

    public function __construct() {
        $this->facultyRepository = new FacultyRepository();
    }

    /**
     * Searches faculties by name.
     *
     * @param string $searchTerm The search term
     * @return FacultyDTO[] Array of matching faculties
     */
    public function searchFaculties(string $searchTerm): array {
        if (empty(trim($searchTerm))) {
            return $this->getAllFaculties();
        }
        return $this->facultyRepository->searchByName($searchTerm);
    }

    /**
     * Gets faculty details by ID.
     *
     * @param int $facultyId The ID of the faculty
     * @return FacultyDTO|null The FacultyDTO object or null if not found
     */
    public function getFacultyDetails(int $facultyId): ?FacultyDTO {
        return $this->facultyRepository->findById($facultyId);
    }

    /**
     * Verifies if the faculty exists 
     *
     * @return bool True if the faculty exists, false otherwise
     */
    public function facultyExists(int $facultyId): bool {
        return $this->facultyRepository->facultyExists($facultyId);
    }

    /**
     * Gets all faculties.
     *
     * @return FacultyDTO[] Array of FacultyDTO objects
     */
    public function getAllFaculties(): array {
        return $this->facultyRepository->findAll();
    }

    /**
     * Creates a new faculty
     * @throws ValidationException if validation fails
     */
    public function createFaculty(string $facultyName): void {
        $exceptionBuilder = ValidationException::build();
        if (empty($facultyName)) {
            $exceptionBuilder->addError(ValidationErrorCode::FACULTY_REQUIRED);
        }
        $exceptionBuilder->throwIfAny();

        $this->facultyRepository->save($facultyName);
    }

    /**
     * Updates faculty data
     * @throws ValidationException if validation fails
     */
    public function updateFaculty(int $facultyId, string $facultyName): void {
        $exceptionBuilder = ValidationException::build();
        $faculty = $this->facultyRepository->findById($facultyId);
        if (!$faculty) {
            $exceptionBuilder->addError(ValidationErrorCode::FACULTY_REQUIRED);
        }

        if (empty($facultyName)) {
            $exceptionBuilder->addError(ValidationErrorCode::FACULTY_REQUIRED);
        }
        $exceptionBuilder->throwIfAny();

        $this->facultyRepository->update($facultyId, $facultyName);
    }

    /**
     * Deletes a faculty
     * @throws ValidationException if validation fails
     */
    public function deleteFaculty(int $facultyId): void {
        $exceptionBuilder = ValidationException::build();
        $faculty = $this->facultyRepository->findById($facultyId);
        if (!$faculty) {
            $exceptionBuilder->addError(ValidationErrorCode::FACULTY_REQUIRED);
        }
        $exceptionBuilder->throwIfAny();

        $this->facultyRepository->delete($facultyId);
    }
}


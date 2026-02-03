<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CourseRepository;
use Unibostu\Model\DTO\CourseDTO;
use Unibostu\Model\Repository\UserCoursesRepository;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;

class CourseService {
    private CourseRepository $courseRepository;
    private UserCoursesRepository $userCoursesRepository;

    public function __construct() {
        $this->courseRepository = new CourseRepository();
        $this->userCoursesRepository = new UserCoursesRepository();
    }

    /**
     * Gets course details by ID
     * @return CourseDTO|null
     */
    public function getCourseDetails(int $courseId): ?CourseDTO {
        return $this->courseRepository->findById($courseId);
    }

    /**
     * Retrieves all courses
     * @return CourseDTO[]
     */
    public function getAllCourses(): array {
        return $this->courseRepository->findAll();
    }

    /**
     * Retrieves courses for a faculty
     * @return CourseDTO[]
     */
    public function getCoursesByFaculty(int $facultyId): array {
        return $this->courseRepository->findByFaculty($facultyId);
    }

    public function getCoursesByFacultyAndUser(int $facultyId, string $userId): array {
        $courses = $this->courseRepository->findByFacultyAndUser($facultyId, $userId);
        return $courses;
    }

    public function searchCoursesByNameAndFaculty(string $searchTerm, int $facultyId): array {
        if (empty(trim($searchTerm))) {
            return $this->getCoursesByFaculty($facultyId);
        }
        return $this->courseRepository->searchByNameAndFaculty($searchTerm, $facultyId);
    }
    
    /**
     * Retrieves courses for a user
     * @return CourseDTO[]
     */
    public function getCoursesByUser(string $userId): array {
        return $this->userCoursesRepository->findCoursesByUser($userId);
    }

    /**
     * Saves user courses
     * @throws ValidationException in case of error
     */
    public function saveUserCourses(string $userId, array $courseIds): void {
        $this->userCoursesRepository->saveUserCourses($userId, $courseIds);
    }

    public function subscribeUserToCourses(string $userId, array $courseIds): void {
        foreach ($courseIds as $courseId) {
            $this->userCoursesRepository->subscribeUserToCourse($userId, $courseId);
        }
    }

    public function unsubscribeUserFromCourses(string $userId, array $courseIds): void {
        foreach ($courseIds as $courseId) {
            $this->userCoursesRepository->unsubscribeUserFromCourse($userId, $courseId);
        }
    }

    public function courseExists(int $courseId): bool {
        return $this->courseRepository->exists($courseId);
    }

    /**
     * Creates a new course
     * @throws ValidationException if data is invalid
     */
    public function createCourse(string $courseName, int $facultyId): void {
        $builder = ValidationException::build();
        
        if (empty($courseName)) {
            $builder->addError(ValidationErrorCode::COURSE_NAME_REQUIRED);
        }

        if ($facultyId <= 0) {
            $builder->addError(ValidationErrorCode::FACULTY_INVALID);
        }
        
        $builder->throwIfAny();
        $this->courseRepository->save($courseName, $facultyId);
    }

    /**
     * Updates course data
     * @throws ValidationException if course does not exist or data is invalid
     */
    public function updateCourse(int $courseId, string $courseName, int $facultyId): void {
        $builder = ValidationException::build();
        
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            $builder->addError(ValidationErrorCode::COURSE_NOT_FOUND);
        }

        if (empty($courseName)) {
            $builder->addError(ValidationErrorCode::COURSE_NAME_REQUIRED);
        }

        if ($facultyId <= 0) {
            $builder->addError(ValidationErrorCode::FACULTY_INVALID);
        }

        $builder->throwIfAny();
        $this->courseRepository->update($courseId, $courseName, $facultyId);
    }

    /**
     * Deletes a course
     * @throws ValidationException if course does not exist
     */
    public function deleteCourse(int $courseId): void {
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            ValidationException::build()
                ->addError(ValidationErrorCode::COURSE_NOT_FOUND)
                ->throwIfAny();
        }

        $this->courseRepository->delete($courseId);
    }
}


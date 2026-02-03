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
     * Gets course details.
     *
     * @param int $courseId Course ID.
     * @return CourseDTO|null Course or null if not found.
     */
    public function getCourseDetails(int $courseId): ?CourseDTO {
        return $this->courseRepository->findById($courseId);
    }

    /**
     * Gets all courses.
     *
     * @return CourseDTO[] All courses.
     */
    public function getAllCourses(): array {
        return $this->courseRepository->findAll();
    }

    /**
     * Gets courses for a faculty.
     *
     * @param int $facultyId Faculty ID.
     * @return CourseDTO[] Faculty courses.
     */
    public function getCoursesByFaculty(int $facultyId): array {
        return $this->courseRepository->findByFaculty($facultyId);
    }

    /**
     * Gets courses for a faculty that a user is enrolled in.
     *
     * @param int $facultyId Faculty ID.
     * @param string $userId User ID.
     * @return CourseDTO[] User's courses in faculty.
     */
    public function getCoursesByFacultyAndUser(int $facultyId, string $userId): array {
        $courses = $this->courseRepository->findByFacultyAndUser($facultyId, $userId);
        return $courses;
    }

    /**
     * Searches courses by name within a faculty.
     *
     * @param string $searchTerm Search term for course name.
     * @param int $facultyId Faculty ID.
     * @return CourseDTO[] Matching courses.
     */
    public function searchCoursesByNameAndFaculty(string $searchTerm, int $facultyId): array {
        if (empty(trim($searchTerm))) {
            return $this->getCoursesByFaculty($facultyId);
        }
        return $this->courseRepository->searchByNameAndFaculty($searchTerm, $facultyId);
    }
    
    /**
     * Gets courses a user is enrolled in.
     *
     * @param string $userId User ID.
     * @return CourseDTO[] User's courses.
     */
    public function getCoursesByUser(string $userId): array {
        return $this->userCoursesRepository->findCoursesByUser($userId);
    }

    /**
     * Saves user course enrollments.
     *
     * @param string $userId User ID.
     * @param array $courseIds Course IDs to enroll.
     * @throws ValidationException
     */
    public function saveUserCourses(string $userId, array $courseIds): void {
        $this->userCoursesRepository->saveUserCourses($userId, $courseIds);
    }

    /**
     * Subscribes a user to multiple courses.
     *
     * @param string $userId User ID.
     * @param array $courseIds Course IDs to subscribe to.
     */
    public function subscribeUserToCourses(string $userId, array $courseIds): void {
        foreach ($courseIds as $courseId) {
            $this->userCoursesRepository->subscribeUserToCourse($userId, $courseId);
        }
    }

    /**
     * Unsubscribes a user from multiple courses.
     *
     * @param string $userId User ID.
     * @param array $courseIds Course IDs to unsubscribe from.
     */
    public function unsubscribeUserFromCourses(string $userId, array $courseIds): void {
        foreach ($courseIds as $courseId) {
            $this->userCoursesRepository->unsubscribeUserFromCourse($userId, $courseId);
        }
    }

    /**
     * Checks if a course exists.
     *
     * @param int $courseId Course ID.
     * @return bool True if course exists.
     */
    public function courseExists(int $courseId): bool {
        return $this->courseRepository->exists($courseId);
    }

    /**
     * Creates a new course.
     *
     * @param string $courseName Course name.
     * @param int $facultyId Faculty ID.
     * @throws ValidationException When course name is empty.
     * @throws ValidationException When faculty ID is invalid.
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
     * Updates a course.
     *
     * @param int $courseId Course ID.
     * @param string $courseName New course name.
     * @param int $facultyId New faculty ID.
     * @throws ValidationException When course does not exist.
     * @throws ValidationException When course name is empty.
     * @throws ValidationException When faculty ID is invalid.
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
     * Deletes a course.
     *
     * @param int $courseId Course ID.
     * @throws ValidationException When course does not exist.
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


<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CourseRepository;
use Unibostu\Model\DTO\CourseDTO;
use Unibostu\Model\Repository\UserCoursesRepository;

class CourseService {
    private CourseRepository $courseRepository;
    private UserCoursesRepository $userCoursesRepository;

    public function __construct() {
        $this->courseRepository = new CourseRepository();
        $this->userCoursesRepository = new UserCoursesRepository();
    }

    /**
     * Ottiene i dettagli di un corso tramite ID
     * @return CourseDTO|null
     */
    public function getCourseDetails(int $courseId): ?CourseDTO {
        return $this->courseRepository->findById($courseId);
    }

    /**
     * Recupera tutti i corsi
     * @return CourseDTO[]
     */
    public function getAllCourses(): array {
        return $this->courseRepository->findAll();
    }

    /**
     * Recupera i corsi di una facolta
     * @return CourseDTO[]
     */
    public function getCoursesByFaculty(int $facultyId): array {
        return $this->courseRepository->findByFaculty($facultyId);
    }

    public function getCoursesByFacultyAndUser(int $facultyId, string $userId): array {
        $courses = $this->courseRepository->findByFacultyAndUser($facultyId, $userId);
        foreach ($courses as $course) {
            $course = $course->withSubscribed(true);
        }
        return $courses;
    }
    
    /**
     * Recupera i corsi di un utente
     * @return CourseDTO[]
     */
    public function getCoursesByUser(string $userId): array {
        return $this->userCoursesRepository->findCoursesByUser($userId);
    }

    /**
     * Salva i corsi di un utente
     * @throws \Exception in caso di errore
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
     * Crea un nuovo corso
     * @throws \Exception se i dati non sono validi
     */
    public function createCourse(string $courseName, int $facultyId): void {
        if (empty($courseName)) {
            throw new \Exception("Nome corso non può essere vuoto");
        }

        if ($facultyId <= 0) {
            throw new \Exception("Facoltà non valida");
        }

        $this->courseRepository->save($courseName, $facultyId);
    }

    /**
     * Aggiorna i dati di un corso
     * @throws \Exception se il corso non esiste o i dati non sono validi
     */
    public function updateCourse(int $courseId, string $courseName, int $facultyId): void {
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            throw new \Exception("Corso non trovato");
        }

        if (empty($courseName)) {
            throw new \Exception("Nome corso non può essere vuoto");
        }

        if ($facultyId <= 0) {
            throw new \Exception("Facoltà non valida");
        }

        $this->courseRepository->update($courseId, $courseName, $facultyId);
    }

    /**
     * Elimina un corso
     * @throws \Exception se il corso non esiste
     */
    public function deleteCourse(int $courseId): void {
        $course = $this->courseRepository->findById($courseId);
        if (!$course) {
            throw new \Exception("Corso non trovato");
        }

        $this->courseRepository->delete($courseId);
    }
}


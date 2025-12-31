<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CourseRepository;
use Unibostu\Model\DTO\CourseDTO;
use Unibostu\Model\Entity\CourseEntity;

class CourseService {
    private CourseRepository $courseRepository;

    public function __construct() {
        $this->courseRepository = new CourseRepository();
    }

    /**
     * Ottiene i dettagli di un corso tramite ID
     */
    public function getCourseDetails(int $idcorso): ?CourseDTO {
        $course = $this->courseRepository->findById($idcorso);
        return $course ? new CourseDTO($course) : null;
    }

    /**
     * Recupera tutti i corsi
     */
    public function getAllCourses(): array {
        $courses = $this->courseRepository->findAll();
        return array_map(fn($course) => new CourseDTO($course), $courses);
    }

    /**
     * Recupera i corsi di una facolta
     */
    public function getCoursesByFaculty(int $idfacolta): array {
        $courses = $this->courseRepository->findByFaculty($idfacolta);
        return array_map(fn($course) => new CourseDTO($course), $courses);
    }

    /**
     * Crea un nuovo corso
     */
    public function createCourse(string $nome_corso, int $idfacolta): bool {
        $course = new CourseEntity(
            0,
            $nome_corso,
            $idfacolta
        );
        return $this->courseRepository->save($course);
    }

    /**
     * Aggiorna i dati di un corso
     */
    public function updateCourse(int $idcorso, string $nome_corso, int $idfacolta): bool {
        $course = new CourseEntity(
            $idcorso,
            $nome_corso,
            $idfacolta
        );
        return $this->courseRepository->update($course);
    }

    /**
     * Elimina un corso
     */
    public function deleteCourse(int $idcorso): bool {
        return $this->courseRepository->delete($idcorso);
    }
}


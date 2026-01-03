<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CourseRepository;
use Unibostu\Model\DTO\CourseDTO;

class CourseService {
    private CourseRepository $courseRepository;

    public function __construct() {
        $this->courseRepository = new CourseRepository();
    }

    /**
     * Ottiene i dettagli di un corso tramite ID
     */
    public function getCourseDetails(int $idcorso): ?CourseDTO {
        return $this->courseRepository->findById($idcorso);
    }

    /**
     * Recupera tutti i corsi
     */
    public function getAllCourses(): array {
        return $this->courseRepository->findAll();
    }

    /**
     * Recupera i corsi di una facolta
     */
    public function getCoursesByFaculty(int $idfacolta): array {
        return $this->courseRepository->findByFaculty($idfacolta);
    }

    /**
     * Crea un nuovo corso
     * @throws \Exception se i dati non sono validi
     */
    public function createCourse(string $nome_corso, int $idfacolta): void {
        if (empty($nome_corso)) {
            throw new \Exception("Nome corso non può essere vuoto");
        }

        if ($idfacolta <= 0) {
            throw new \Exception("Facoltà non valida");
        }

        $this->courseRepository->save($nome_corso, $idfacolta);
    }

    /**
     * Aggiorna i dati di un corso
     * @throws \Exception se il corso non esiste o i dati non sono validi
     */
    public function updateCourse(int $idcorso, string $nome_corso, int $idfacolta): void {
        $course = $this->courseRepository->findById($idcorso);
        if (!$course) {
            throw new \Exception("Corso non trovato");
        }

        if (empty($nome_corso)) {
            throw new \Exception("Nome corso non può essere vuoto");
        }

        if ($idfacolta <= 0) {
            throw new \Exception("Facoltà non valida");
        }

        $this->courseRepository->update($idcorso, $nome_corso, $idfacolta);
    }

    /**
     * Elimina un corso
     * @throws \Exception se il corso non esiste
     */
    public function deleteCourse(int $idcorso): void {
        $course = $this->courseRepository->findById($idcorso);
        if (!$course) {
            throw new \Exception("Corso non trovato");
        }

        $this->courseRepository->delete($idcorso);
    }
}


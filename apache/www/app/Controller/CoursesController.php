<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Model\Service\CourseService;

class CoursesController {
    private CourseService $courseService;

    public function __construct() {
        $this->courseService = new CourseService();
    }

    /**
     * Carica i dettagli di un corso
     */
    public function loadCourseDetails(int $idcorso): void {
        $courseDTO = $this->courseService->getCourseDetails($idcorso);
        // Passa i dati alla view
        // $this->view('course/details', ['courseDTO' => $courseDTO]);
    }

    /**
     * Carica la lista di tutti i corsi
     */
    public function loadAllCourses(): void {
        $courses = $this->courseService->getAllCourses();
        // Passa i dati alla view
        // $this->view('course/list', ['courses' => $courses]);
    }

    /**
     * Carica i corsi di una facolta
     */
    public function loadCoursesByFaculty(int $idfacolta): void {
        $courses = $this->courseService->getCoursesByFaculty($idfacolta);
        // Passa i dati alla view
        // $this->view('course/faculty-list', ['courses' => $courses, 'idfacolta' => $idfacolta]);
    }

    /**
     * Crea un nuovo corso
     */
    public function createCourse(string $nome_corso, int $idfacolta): void {
        $result = $this->courseService->createCourse($nome_corso, $idfacolta);
        // Gestisci il risultato
    }

    /**
     * Aggiorna i dati di un corso
     */
    public function updateCourse(int $idcorso, string $nome_corso, int $idfacolta): void {
        $result = $this->courseService->updateCourse($idcorso, $nome_corso, $idfacolta);
        // Gestisci il risultato
    }

    /**
     * Elimina un corso
     */
    public function deleteCourse(int $idcorso): void {
        $result = $this->courseService->deleteCourse($idcorso);
        // Gestisci il risultato
    }
}


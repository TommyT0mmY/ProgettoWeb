<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Model\Service\FacultyService;

class FacultyController {
    private FacultyService $facultyService;

    public function __construct() {
        $this->facultyService = new FacultyService();
    }

    /**
     * Carica i dettagli di una facolta
     */
    public function loadFacultyDetails(int $idfacolta): void {
        $facultyDTO = $this->facultyService->getFacultyDetails($idfacolta);
        // Passa i dati alla view
        // $this->view('faculty/details', ['facultyDTO' => $facultyDTO]);
    }

    /**
     * Carica la lista di tutte le facolta
     */
    public function loadAllFaculties(): void {
        $faculties = $this->facultyService->getAllFaculties();
        // Passa i dati alla view
        // $this->view('faculty/list', ['faculties' => $faculties]);
    }

    /**
     * Crea una nuova facolta
     */
    public function createFaculty(string $nome_facolta): void {
        $result = $this->facultyService->createFaculty($nome_facolta);
        // Gestisci il risultato
    }

    /**
     * Aggiorna i dati di una facolta
     */
    public function updateFaculty(int $idfacolta, string $nome_facolta): void {
        $result = $this->facultyService->updateFaculty($idfacolta, $nome_facolta);
        // Gestisci il risultato
    }

    /**
     * Elimina una facolta
     */
    public function deleteFaculty(int $idfacolta): void {
        $result = $this->facultyService->deleteFaculty($idfacolta);
        // Gestisci il risultato
    }
}

?>

<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\FacultyRepository;
use Unibostu\Model\DTO\FacultyDTO;

class FacultyService {
    private FacultyRepository $facultyRepository;

    public function __construct() {
        $this->facultyRepository = new FacultyRepository();
    }

    /**
     * Ottiene i dettagli di una facolta tramite ID
     */
    public function getFacultyDetails(int $idfacolta): ?FacultyDTO {
        return $this->facultyRepository->findById($idfacolta);
    }

    /**
     * Recupera tutte le facolta
     */
    public function getAllFaculties(): array {
        return $this->facultyRepository->findAll();
    }

    /**
     * Crea una nuova facolta
     * @throws \Exception se i dati non sono validi
     */
    public function createFaculty(string $nome_facolta): void {
        if (empty($nome_facolta)) {
            throw new \Exception("Nome facoltà non può essere vuoto");
        }

        $this->facultyRepository->save($nome_facolta);
    }

    /**
     * Aggiorna i dati di una facolta
     * @throws \Exception se la facoltà non esiste o i dati non sono validi
     */
    public function updateFaculty(int $idfacolta, string $nome_facolta): void {
        $faculty = $this->facultyRepository->findById($idfacolta);
        if (!$faculty) {
            throw new \Exception("Facoltà non trovata");
        }

        if (empty($nome_facolta)) {
            throw new \Exception("Nome facoltà non può essere vuoto");
        }

        $this->facultyRepository->update($idfacolta, $nome_facolta);
    }

    /**
     * Elimina una facolta
     * @throws \Exception se la facoltà non esiste
     */
    public function deleteFaculty(int $idfacolta): void {
        $faculty = $this->facultyRepository->findById($idfacolta);
        if (!$faculty) {
            throw new \Exception("Facoltà non trovata");
        }

        $this->facultyRepository->delete($idfacolta);
    }
}


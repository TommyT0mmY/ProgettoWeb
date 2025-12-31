<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\FacultyRepository;
use Unibostu\Model\DTO\FacultyDTO;
use Unibostu\Model\Entity\FacultyEntity;

class FacultyService {
    private FacultyRepository $facultyRepository;

    public function __construct() {
        $this->facultyRepository = new FacultyRepository();
    }

    /**
     * Ottiene i dettagli di una facolta tramite ID
     */
    public function getFacultyDetails(int $idfacolta): ?FacultyDTO {
        $faculty = $this->facultyRepository->findById($idfacolta);
        return $faculty ? new FacultyDTO($faculty) : null;
    }

    /**
     * Recupera tutte le facolta
     */
    public function getAllFaculties(): array {
        $faculties = $this->facultyRepository->findAll();
        return array_map(fn($faculty) => new FacultyDTO($faculty), $faculties);
    }

    /**
     * Crea una nuova facolta
     */
    public function createFaculty(string $nome_facolta): bool {
        $faculty = new FacultyEntity(
            0,
            $nome_facolta
        );
        return $this->facultyRepository->save($faculty);
    }

    /**
     * Aggiorna i dati di una facolta
     */
    public function updateFaculty(int $idfacolta, string $nome_facolta): bool {
        $faculty = new FacultyEntity(
            $idfacolta,
            $nome_facolta
        );
        return $this->facultyRepository->update($faculty);
    }

    /**
     * Elimina una facolta
     */
    public function deleteFaculty(int $idfacolta): bool {
        return $this->facultyRepository->delete($idfacolta);
    }
}


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
     * Crea una nuova facolta
     * @throws \Exception se i dati non sono validi
     */
    public function createFaculty(string $facultyName): void {
        if (empty($facultyName)) {
            throw new \Exception("Nome facoltà non può essere vuoto");
        }

        $this->facultyRepository->save($facultyName);
    }

    /**
     * Aggiorna i dati di una facolta
     * @throws \Exception se la facoltà non esiste o i dati non sono validi
     */
    public function updateFaculty(int $facultyId, string $facultyName): void {
        $faculty = $this->facultyRepository->findById($facultyId);
        if (!$faculty) {
            throw new \Exception("Facoltà non trovata");
        }

        if (empty($facultyName)) {
            throw new \Exception("Nome facoltà non può essere vuoto");
        }

        $this->facultyRepository->update($facultyId, $facultyName);
    }

    /**
     * Elimina una facolta
     * @throws \Exception se la facoltà non esiste
     */
    public function deleteFaculty(int $facultyId): void {
        $faculty = $this->facultyRepository->findById($facultyId);
        if (!$faculty) {
            throw new \Exception("Facoltà non trovata");
        }

        $this->facultyRepository->delete($facultyId);
    }
}


<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\TagRepository;

class TagService {
    private TagRepository $tagRepository;

    public function __construct() {
        $this->tagRepository = new TagRepository();
    }

    /**
     * Recupera tutti i tag di un corso
     */
    public function getTagsByCourse(int $courseId): array {
        return $this->tagRepository->findByCourse($courseId);
    }

    /**
     * Crea un nuovo tag
     * @throws \Exception se i dati non sono validi
     */
    public function createTag(string $type, int $courseId): void {
        if (empty($type)) {
            throw new \Exception("Tipo tag non può essere vuoto");
        }

        if ($courseId <= 0) {
            throw new \Exception("Corso non valido");
        }

        $existing = $this->tagRepository->findByTypeAndCourse($type, $courseId);
        if ($existing) {
            throw new \Exception("Tag '$type' per questo corso esiste già");
        }

        $this->tagRepository->save($type, $courseId);
    }

    /**
     * Aggiorna un tag
     * @throws \Exception se il tag non esiste o i dati non sono validi
     */
    public function updateTag(int $tagId, string $type, int $courseId): void {
        if (empty($type)) {
            throw new \Exception("Tipo tag non può essere vuoto");
        }

        if ($courseId <= 0) {
            throw new \Exception("Corso non valido");
        }

        $tag = $this->tagRepository->findByIdAndCourse($tagId, $courseId);
        if (!$tag) {
            throw new \Exception("Tag non trovato");
        }

        $this->tagRepository->update($tagId, $type);
    }

    /**
     * Elimina un tag
     * @throws \Exception se il tag non esiste
     */
    public function deleteTag(int $tagId, int $courseId): void {
        $tag = $this->tagRepository->findByIdAndCourse($tagId, $courseId);
        if (!$tag) {
            throw new \Exception("Tag non trovato");
        }

        $this->tagRepository->delete($tagId, $courseId);
    }
}

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
     * Recupera i tag di un post
     */
    public function getTagsByPost(int $postId): array {
        return $this->tagRepository->findByPost($postId);
    }
    
    /**
     * Crea un nuovo tag
     * @throws \Exception se i dati non sono validi
     */
    public function createTag(string $tag_name, int $courseId): void {
        if (empty($tag_name)) {
            throw new \Exception("Tipo tag non può essere vuoto");
        }

        if ($courseId <= 0) {
            throw new \Exception("Corso non valido");
        }

        $existing = $this->tagRepository->findByTypeAndCourse($tag_name, $courseId);
        if ($existing) {
            throw new \Exception("Tag '$tag_name' per questo corso esiste già");
        }

        $this->tagRepository->save($tag_name, $courseId);
    }

    /**
     * Aggiorna un tag
     * @throws \Exception se il tag non esiste o i dati non sono validi
     */
    public function updateTag(int $tagId, string $tag_name, int $courseId): void {
        if (empty($tag_name)) {
            throw new \Exception("Tipo tag non può essere vuoto");
        }

        if ($courseId <= 0) {
            throw new \Exception("Corso non valido");
        }

        $tag = $this->tagRepository->findByIdAndCourse($tagId, $courseId);
        if (!$tag) {
            throw new \Exception("Tag non trovato");
        }

        $this->tagRepository->update($tagId, $tag_name);
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

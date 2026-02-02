<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\TagRepository;
use Unibostu\Model\DTO\TagDTO;
use Unibostu\Core\PostRepository;
use Unibostu\Model\Repository\PostTagRepository;

class TagService {
    private TagRepository $tagRepository;
    private PostTagRepository $postTagRepository;

    public function __construct() {
        $this->tagRepository = new TagRepository();
        $this->postTagRepository = new PostTagRepository();
    }

    /**
     * Recupera tutti i tag di un corso
     */
    public function getTagsByCourse(int $courseId): array {
        return $this->tagRepository->findByCourse($courseId);
    }

    /**
     * Searches tags by name within a specific course.
     *
     * @param string $searchTerm The search term
     * @param int $courseId The course ID to filter by
     * @return TagDTO[] Array of matching tags
     */
    public function searchTags(string $searchTerm, int $courseId): array {
        if (empty(trim($searchTerm))) {
            return $this->getTagsByCourse($courseId);
        }
        return $this->tagRepository->searchByName($searchTerm);
    }

    /**
     * Recupera i tag di un post
     * @return array Array di array con chiavi TagDTO
     */
    public function getTagsByPost(int $postId): array {
        return $this->postTagRepository->findTagsByPost($postId);
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

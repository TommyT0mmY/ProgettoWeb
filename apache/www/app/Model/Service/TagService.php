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
    public function getTagsByCourse(int $idcorso): array {
        return $this->tagRepository->findByCourse($idcorso);
    }

    /**
     * Crea un nuovo tag
     * @throws \Exception se i dati non sono validi
     */
    public function createTag(string $tipo, int $idcorso): void {
        if (empty($tipo)) {
            throw new \Exception("Tipo tag non può essere vuoto");
        }

        if ($idcorso <= 0) {
            throw new \Exception("Corso non valido");
        }

        $existing = $this->tagRepository->findByTypeAndCourse($tipo, $idcorso);
        if ($existing) {
            throw new \Exception("Tag '$tipo' per questo corso esiste già");
        }

        $this->tagRepository->save($tipo, $idcorso);
    }

    /**
     * Elimina un tag
     * @throws \Exception se il tag non esiste
     */
    public function deleteTag(int $idtag, int $idcorso): void {
        $tag = $this->tagRepository->findByIdAndCourse($idtag, $idcorso);
        if (!$tag) {
            throw new \Exception("Tag non trovato");
        }

        $this->tagRepository->delete($idtag, $idcorso);
    }
}

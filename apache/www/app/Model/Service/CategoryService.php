<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CategoryRepository;
use Unibostu\Model\DTO\CategoryDTO;

class CategoryService {
    private CategoryRepository $categoryRepository;

    public function __construct() {
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * Recupera una categoria tramite ID
     */
    public function getCategory(int $idcategoria): ?CategoryDTO {
        return $this->categoryRepository->findById($idcategoria);
    }

    /**
     * Recupera tutte le categorie
     */
    public function getAllCategories(): array {
        return $this->categoryRepository->findAll();
    }

    /**
     * Crea una nuova categoria
     * @throws \Exception se i dati non sono validi
     */
    public function createCategory(string $nome_categoria): void {
        if (empty($nome_categoria)) {
            throw new \Exception("Nome categoria non può essere vuoto");
        }

        $this->categoryRepository->save($nome_categoria);
    }

    /**
     * Elimina una categoria
     * @throws \Exception se la categoria non esiste
     */
    public function deleteCategory(int $idcategoria): void {
        $category = $this->categoryRepository->findById($idcategoria);
        if (!$category) {
            throw new \Exception("Categoria '$idcategoria' non trovata");
        }

        $this->categoryRepository->delete($idcategoria);
    }
}

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
    public function getCategory(int $categoryId): ?CategoryDTO {
        return $this->categoryRepository->findById($categoryId);
    }

    /**
     * Recupera tutte le categorie
     */
    public function getAllCategories(): array {
        return $this->categoryRepository->findAll();
    }

    /**
     * Search categories by name
     */
    public function searchCategoriesByName(string $searchTerm): array {
        if (empty(trim($searchTerm))) {
            return $this->getAllCategories();
        }
        return $this->categoryRepository->searchByName($searchTerm);
    }

    /**
     * Crea una nuova categoria
     * @throws \Exception se i dati non sono validi
     */
    public function createCategory(string $categoryName): void {
        if (empty($categoryName)) {
            throw new \Exception("Nome categoria non può essere vuoto");
        }
        if ($this->categoryRepository->findByName($categoryName)) {
            throw new \Exception("Categoria '$categoryName' già esistente");
        }

        $this->categoryRepository->save($categoryName);
    }

    /**
     * Aggiorna i dati di una categoria
     * @throws \Exception se la categoria non esiste o i dati non sono validi
     */
    public function updateCategory(int $categoryId, string $categoryName): void {
        $category = $this->categoryRepository->findById($categoryId);
        if (!$category) {
            throw new \Exception("Categoria non trovata");
        }

        if (empty($categoryName)) {
            throw new \Exception("Nome categoria non può essere vuoto");
        }
        $this->categoryRepository->update($categoryId, $categoryName);
    }

    /**
     * Elimina una categoria
     * @throws \Exception se la categoria non esiste
     */
    public function deleteCategory(int $categoryId): void {
        $category = $this->categoryRepository->findById($categoryId);
        if (!$category) {
            throw new \Exception("Categoria '$categoryId' non trovata");
        }

        $this->categoryRepository->delete($categoryId);
    }
}

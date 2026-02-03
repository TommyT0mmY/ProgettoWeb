<?php
declare(strict_types=1);

namespace Unibostu\Model\Service;

use Unibostu\Model\Repository\CategoryRepository;
use Unibostu\Model\DTO\CategoryDTO;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;

class CategoryService {
    private CategoryRepository $categoryRepository;

    public function __construct() {
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * Retrieves a category by ID
     */
    public function getCategory(int $categoryId): ?CategoryDTO {
        return $this->categoryRepository->findById($categoryId);
    }

    /**
     * Retrieves all categories
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
     * Creates a new category
     * @throws ValidationException if data is invalid
     */
    public function createCategory(string $categoryName): void {
        $builder = ValidationException::build();
        
        if (empty($categoryName)) {
            $builder->addError(ValidationErrorCode::CATEGORY_NAME_REQUIRED);
        }
        if ($this->categoryRepository->findByName($categoryName)) {
            $builder->addError(ValidationErrorCode::CATEGORY_ALREADY_EXISTS);
        }
        
        $builder->throwIfAny();
        $this->categoryRepository->save($categoryName);
    }

    /**
     * Updates category data
     * @throws ValidationException if category does not exist or data is invalid
     */
    public function updateCategory(int $categoryId, string $categoryName): void {
        $builder = ValidationException::build();
        
        $category = $this->categoryRepository->findById($categoryId);
        if (!$category) {
            $builder->addError(ValidationErrorCode::CATEGORY_NOT_FOUND);
        }
        
        if (empty($categoryName)) {
            $builder->addError(ValidationErrorCode::CATEGORY_NAME_REQUIRED);
        }
        
        $builder->throwIfAny();
        $this->categoryRepository->update($categoryId, $categoryName);
    }

    /**
     * Deletes a category
     * @throws ValidationException if category does not exist
     */
    public function deleteCategory(int $categoryId): void {
        $category = $this->categoryRepository->findById($categoryId);
        if (!$category) {
            ValidationException::build()
                ->addError(ValidationErrorCode::CATEGORY_NOT_FOUND)
                ->throwIfAny();
        }

        $this->categoryRepository->delete($categoryId);
    }
}

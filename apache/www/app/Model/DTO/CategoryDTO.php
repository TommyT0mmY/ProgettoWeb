<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\CategoryEntity;

class CategoryDTO {
    public CategoryEntity $category;

    public function __construct(CategoryEntity $category) {
        $this->category = $category;
    }
}

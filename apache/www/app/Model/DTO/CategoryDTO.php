<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CategoryDTO {
    public int $categoryId;
    public string $categoryName;

    public function __construct(
        int $categoryId,
        string $categoryName
    ) {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
    }
}

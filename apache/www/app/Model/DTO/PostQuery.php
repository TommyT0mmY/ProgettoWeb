<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class PostQuery {
    /** @var int course ID (null for homepage) */
    private ?int $courseId = null;
    /** @var int Array of category IDs */
    private ?int $category = null;
    /** @var array[['tagId' => int, 'courseId' => int]] Array of tags ['tagId' => int, 'courseId' => int] (for course filtering) */
    private array $tags = [];
    /** Sort order: 'ASC' for ascending, 'DESC' for descending */
    private string $sortOrder = 'DESC';
    /** Last element ID for pagination (if ($sortOrder = 'DESC') => $lastPostId = PHP_INT_MAX; else 0) for first page */
    private int $lastPostId = PHP_INT_MAX;
    /** Limit for query results */
    private int $limit = 5;
    /** User ID for filtering posts by author */
    private ?string $authorId = null;
    /** User ID for filtering posts by user */
    private ?string $userId = null;
    /** Admin view */
    private bool $isAdminView = false;

    private function __construct(
    ) {
    }

    public static function create(): self {
        return new self();
    }

    public function forAdmin(?bool $isAdminView): self {
        if ($isAdminView !== null) $this->isAdminView = $isAdminView;
        return $this;
    }

    public function forUser(?string $userId): self {
        if ($userId !== null) $this->userId = $userId;
        return $this;
    }

    public function authoredBy(?string $authorId): self {
        $this->authorId = $this->typeCorrection($authorId, string::class);
        return $this;
    }

    public function inCourse(null|int|string $courseId): self {
        $this->courseId = $this->typeCorrection($courseId, 'int');
        return $this;
    }

    public function inCategory(null|int|string $categoryId): self {
        $this->category = $this->typeCorrection($categoryId, 'int');
        return $this;
    }

    public function withTags(?array $tags): self {
        $this->tags = $this->typeCorrection($tags, 'array');
        return $this;
    }

    public function sortedBy(?string $sortOrder): self {
        if ($sortOrder !== null) {
            $this->sortOrder = in_array($sortOrder, ['asc', 'desc']) ? strtoupper($sortOrder) : 'DESC';
        }
        // This may be removed later since its information that should be given by js
        if ($this->sortOrder === 'DESC' && $this->lastPostId === 0) {
            $this->lastPostId = PHP_INT_MAX;
        } else if ($this->sortOrder === 'ASC' && $this->lastPostId === PHP_INT_MAX) {
            $this->lastPostId = 0;
        }
        return $this;
    }

    public function afterPost(int|string $lastPostId): self {
        $this->lastPostId = $this->typeCorrection($lastPostId, 'int');
        return $this;
    }

    public function withLimit(int|string $limit): self {
        $this->limit = $this->typeCorrection($limit, 'int');
        return $this;
    }

    public function getIsAdminView(): bool {
        return $this->isAdminView;
    }

    public function getUserId(): ?string {
        return $this->userId;
    }

    public function getAuthorId(): ?string {
        return $this->authorId;
    }

    public function getCourseId(): ?int {
        return $this->courseId;
    }

    public function getCategory(): ?int {
        return $this->category;
    }

    public function getTags(): array {
        return $this->tags;
    }

    public function getSortOrder(): string {
        return $this->sortOrder;
    }

    public function getLastPostId(): int {
        return $this->lastPostId;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    private function typeCorrection(mixed $value,  $type): mixed {
        if ($value === null && $type !== 'array') {
            return null;
        }
        switch ($type) {
            case 'int':
                return (int)$value;
            case 'string':
                return (string)$value;
            case 'bool':
                return (bool)$value;
            case 'array':
                return (array)$value;
            default:
                return $value;
        }
    }
}

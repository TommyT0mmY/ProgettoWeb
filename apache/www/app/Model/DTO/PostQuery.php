<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class PostQuery {
    /** @var int course ID (null for homepage) */
    private ?int $courseId = null;
    /** @var int Array of category IDs */
    private ?int $category = null;
    /** @var array[] Array of tags ['tagId' => int, 'courseId' => int] (for course filtering) */
    private array $tags = [];
    /** Sort order: 'ASC' for ascending, 'DESC' for descending */
    private string $sortOrder = 'DESC';
    /** Last element ID for pagination (if ($sortOrder = 'DESC') => $lastPostId = PHP_INT_MAX; else 0) for first page */
    private int $lastPostId = PHP_INT_MAX;
    /** Limit for query results */
    private int $limit = 10;
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

    public function forAdmin(bool $isAdminView): self {
        $this->isAdminView = $isAdminView;
        return $this;
    }

    public function forUser(string $userId): self {
        $this->userId = $userId;
        return $this;
    }

    public function authoredBy(string $authorId): self {
        $this->authorId = $authorId;
        return $this;
    }

    public function inCourse(int $courseId): self {
        $this->courseId = $courseId;
        return $this;
    }

    public function inCategory(int $categoryId): self {
        $this->category = $categoryId;
        return $this;
    }

    public function withTags(array $tags): self {
        $this->tags = $tags;
        return $this;
    }

    public function sortedBy(string $sortOrder): self {
        $this->sortOrder = in_array($sortOrder, ['ASC', 'DESC']) ? $sortOrder : 'DESC';
        return $this;
    }

    public function afterPost(int $lastPostId): self {
        $this->lastPostId = $lastPostId;
        return $this;
    }

    public function withLimit(int $limit): self {
        $this->limit = $limit;
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
}

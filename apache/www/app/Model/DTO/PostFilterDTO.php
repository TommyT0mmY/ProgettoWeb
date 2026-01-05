<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PostFilterDTO {
    /** @var int course ID (null for homepage) */
    public ?int $courseId;
    /** @var int Array of category IDs */
    public ?int $category;
    /** @var array[] Array of tags ['tagId' => int, 'courseId' => int] (for course filtering) */
    public array $tags;
    /** Sort order: 'ASC' for ascending, 'DESC' for descending */
    public string $sortOrder;
    /** Last element ID for pagination (if ($sortOrder = 'DESC') => $lastPostId = PHP_INT_MAX; else 0) for first page */
    public int $lastPostId;
    /** Limit for query results */
    public int $limit;
    /** User ID for filtering posts by author */
    public ?string $authorId;

    public function __construct(
        array $tags = [],
        string $sortOrder = 'DESC',
        int $lastPostId = PHP_INT_MAX,
        int $limit = 10,
        ?int $courseId = null,
        ?string $authorId = null,
        ?int $category = null,
    ) {
        $this->courseId = $courseId;
        $this->category = $category;
        $this->tags = $tags;
        $this->sortOrder = in_array($sortOrder, ['ASC', 'DESC']) ? $sortOrder : 'DESC';
        $this->lastPostId = $lastPostId;
        $this->limit = $limit;
        $this->authorId = $authorId;
    }
}

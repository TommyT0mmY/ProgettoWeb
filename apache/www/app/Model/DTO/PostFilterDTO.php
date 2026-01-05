<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PostFilterDTO {
    /** @var int course ID (null for homepage) */
    public ?int $corso;
    /** @var int Array of category IDs */
    public ?int $category;
    /** @var array[] Array of tags ['idtag' => int, 'idcorso' => int] (for course filtering) */
    public array $tags;
    /** Sort order: 'ASC' for ascending, 'DESC' for descending */
    public string $ordinamento;
    /** Last element ID for pagination (if ($ordinamento = 'DESC') => $lastPostId = PHP_INT_MAX; else 0) for first page */
    public int $lastPostId;
    /** Limit for query results */
    public int $limit;
    /** User ID for filtering posts by author */
    public ?string $authorId;

    public function __construct(
        array $tags = [],
        string $ordinamento = 'DESC',
        int $lastPostId = PHP_INT_MAX,
        int $limit = 10,
        ?int $corso = null,
        ?string $authorId = null,
        ?int $category = null,
    ) {
        $this->corso = $corso;
        $this->category = $category;
        $this->tags = $tags;
        $this->ordinamento = in_array($ordinamento, ['ASC', 'DESC']) ? $ordinamento : 'DESC';
        $this->lastPostId = $lastPostId;
        $this->limit = $limit;
        $this->authorId = $authorId;
    }
}

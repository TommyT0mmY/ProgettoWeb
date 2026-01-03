<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PostFilterDTO {
    /** @var int course ID (null for homepage) */
    public ?int $corso;
    /** @var string[] Array of category IDs */
    public array $categorie;
    /** @var array[] Array of tags ['tipo' => string, 'idcorso' => int] (for course filtering) */
    public array $tags;
    /** Sort order: 'ASC' for ascending, 'DESC' for descending */
    public string $ordinamento;
    /** Last element ID for pagination */
    public int $lastId;
    /** Limit for query results */
    public int $limit;

    public function __construct(
        ?int $corso = null,
        array $categorie = [],
        array $tags = [],
        string $ordinamento,
        int $lastId,
        int $limit = 10
    ) {
        $this->corso = $corso;
        $this->categorie = $categorie;
        $this->tags = $tags;
        $this->ordinamento = in_array($ordinamento, ['ASC', 'DESC']) ? $ordinamento : 'DESC';
        $this->lastId = $lastId;
        $this->limit = $limit;
    }
}

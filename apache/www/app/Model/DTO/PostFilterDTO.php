<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class PostFilterDTO {
    /** @var int[] Array of course IDs (for homepage filtering) */
    public array $corsi;
    /** @var string[] Array of category IDs */
    public array $categorie;
    /** @var array[] Array of tags ['tipo' => string, 'idcorso' => int] (for course filtering) */
    public array $tags;
    /** Sort order: 'ASC' for ascending, 'DESC' for descending */
    public string $ordinamento;
    /** Limit for query results */
    public int $limit;
    /** Offset for pagination */
    public int $offset;

    public function __construct(
        array $corsi = [],
        array $categorie = [],
        array $tags = [],
        string $ordinamento = 'DESC',
        int $limit = 50,
        int $offset = 0
    ) {
        $this->corsi = $corsi;
        $this->categorie = $categorie;
        $this->tags = $tags;
        $this->ordinamento = in_array($ordinamento, ['ASC', 'DESC']) ? $ordinamento : 'DESC';
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function hasFilters(): bool {
        return !empty($this->corsi) || !empty($this->categorie) || !empty($this->tags);
    }
}

<?php
declare(strict_types=1);

namespace Unibostu\Model\Entity;

class CategoryEntity {
    public string $idcategoria;
    public bool $riservata;

    public function __construct(
        string $idcategoria,
        bool $riservata
    ) {
        $this->idcategoria = $idcategoria;
        $this->riservata = $riservata;
    }
}

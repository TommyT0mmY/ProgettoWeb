<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CategoryDTO {
    public int $idcategoria;
    public string $nome_categoria;

    public function __construct(
        int $idcategoria,
        string $nome_categoria
    ) {
        $this->idcategoria = $idcategoria;
        $this->nome_categoria = $nome_categoria;
    }
}

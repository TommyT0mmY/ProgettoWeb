<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class TagDTO {
    public string $tipo;
    public int $idcorso;

    public function __construct(
        string $tipo,
        int $idcorso
    ) {
        $this->tipo = $tipo;
        $this->idcorso = $idcorso;
    }
}

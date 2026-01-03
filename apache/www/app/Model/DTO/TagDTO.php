<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class TagDTO {
    public int $idtag;
    public string $tipo;
    public int $idcorso;

    public function __construct(
        int $idtag,
        string $tipo,
        int $idcorso
    ) {
        $this->idtag = $idtag;
        $this->tipo = $tipo;
        $this->idcorso = $idcorso;
    }
}

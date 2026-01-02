<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class CreateCommentDTO {
    public int $idpost;
    public string $testo;
    public string $idutente;
    public ?int $idcommento_genitore;

    public function __construct(
        int $idpost,
        string $testo,
        string $idutente,
        ?int $idcommento_genitore = null
    ) {
        $this->idpost = $idpost;
        $this->testo = $testo;
        $this->idutente = $idutente;
        $this->idcommento_genitore = $idcommento_genitore;
    }
}

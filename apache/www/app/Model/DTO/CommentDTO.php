<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class CommentDTO {
    public ?int $idcommento = null;
    public ?int $idpost = null;
    public ?string $testo = null;
    public ?string $data_creazione = null;
    public bool $cancellato = false;
    public ?string $idutente = null;
    public ?int $idpost_genitore = null;
    public ?int $idcommento_genitore = null;

    public function __construct(
        ?int $idcommento = null,
        ?int $idpost = null,
        ?string $testo = null,
        ?string $data_creazione = null,
        bool $cancellato = false,
        ?string $idutente = null,
        ?int $idpost_genitore = null,
        ?int $idcommento_genitore = null
    ) {
        $this->idcommento = $idcommento;
        $this->idpost = $idpost;
        $this->testo = $testo;
        $this->data_creazione = $data_creazione;
        $this->cancellato = $cancellato;
        $this->idutente = $idutente;
        $this->idpost_genitore = $idpost_genitore;
        $this->idcommento_genitore = $idcommento_genitore;
    }
}

<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CommentDTO {
    public int $idcommento;
    public int $idpost;
    public string $testo;
    public string $data_creazione;
    public bool $cancellato;
    public string $idutente;
    public ?int $idcommento_genitore;

    public function __construct(
        int $idcommento,
        int $idpost,
        string $testo,
        string $data_creazione,
        string $idutente,
        bool $cancellato = false,
        ?int $idcommento_genitore = null
    ) {
        $this->idcommento = $idcommento;
        $this->idpost = $idpost;
        $this->testo = $testo;
        $this->data_creazione = $data_creazione;
        $this->cancellato = $cancellato;
        $this->idutente = $idutente;
        $this->idcommento_genitore = $idcommento_genitore;
    }
}

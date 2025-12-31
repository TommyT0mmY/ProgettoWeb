<?php
declare(strict_types=1);

namespace Unibostu\Model\Entity;

class CommentEntity {
    public int $idpost;
    public int $idcommento;
    public string $testo;
    public string $data_creazione;
    public bool $cancellato;
    public int $identita;
    public int $idpost_genitore;
    public int $idcommento_genitore;

    public function __construct(
        int $idpost,
        int $idcommento,
        string $testo,
        string $data_creazione,
        bool $cancellato,
        int $identita,
        int $idpost_genitore,
        int $idcommento_genitore
    ) {
        $this->idpost = $idpost;
        $this->idcommento = $idcommento;
        $this->testo = $testo;
        $this->data_creazione = $data_creazione;
        $this->cancellato = $cancellato;
        $this->identita = $identita;
        $this->idpost_genitore = $idpost_genitore;
        $this->idcommento_genitore = $idcommento_genitore;
    }
}
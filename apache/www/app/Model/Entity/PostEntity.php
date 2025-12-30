<?php
declare(strict_types=1);

namespace Unibostu\Model\Entity;

class PostEntity {
    public int $idpost;
    public string $titolo;
    public string $descrizione;
    public ?string $percorso_allegato;
    public int $likes;
    public int $dislikes;
    public string $data_creazione;
    public int $identita;
    public ?int $idcorso;

    public function __construct(
        int $idpost,
        string $titolo,
        string $descrizione,
        ?string $percorso_allegato,
        int $likes,
        int $dislikes,
        string $data_creazione,
        int $identita,
        ?int $idcorso
    ) {
        $this->idpost = $idpost;
        $this->titolo = $titolo;
        $this->descrizione = $descrizione;
        $this->percorso_allegato = $percorso_allegato;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
        $this->data_creazione = $data_creazione;
        $this->identita = $identita;
        $this->idcorso = $idcorso;
    }
}

?>
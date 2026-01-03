<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PostDTO {
    public int $idpost;
    public string $titolo;
    public string $descrizione;
    public ?string $percorso_allegato;
    public string $data_creazione;
    public string $idutente;
    public int $idcorso;
    /** @var array Array of tag arrays with 'tipo' and 'idcorso' keys */
    public array $tags;
    /** @var array Array of category IDs */
    public array $categorie;

    public function __construct(
        int $idpost,
        string $titolo,
        string $descrizione,
        ?string $percorso_allegato = null,
        string $data_creazione,
        string $idutente,
        int $idcorso,
        array $tags = [],
        array $categorie = []
    ) {
        $this->idpost = $idpost;
        $this->titolo = $titolo;
        $this->descrizione = $descrizione;
        $this->percorso_allegato = $percorso_allegato;
        $this->data_creazione = $data_creazione;
        $this->idutente = $idutente;
        $this->idcorso = $idcorso;
        $this->tags = $tags;
        $this->categorie = $categorie;
    }
}

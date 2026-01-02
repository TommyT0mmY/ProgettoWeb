<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class CreateUserPostDTO {
    public string $idutente;
    public int $idcorso;
    public string $titolo;
    public string $descrizione;
    public ?string $percorso_allegato;
    public array $tags;
    public array $categorie;

    public function __construct(
        string $idutente,
        int $idcorso,
        string $titolo,
        string $descrizione,
        ?string $percorso_allegato = null,
        array $tags = [],
        array $categorie = []
    ) {
        $this->idutente = $idutente;
        $this->idcorso = $idcorso;
        $this->titolo = $titolo;
        $this->descrizione = $descrizione;
        $this->percorso_allegato = $percorso_allegato;
        $this->tags = $tags;
        $this->categorie = $categorie;
    }
}

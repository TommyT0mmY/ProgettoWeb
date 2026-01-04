<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CreateUserPostDTO {
    public string $idutente;
    public int $idcorso;
    public string $titolo;
    public string $descrizione;
    /** @var array<int> Array of tag IDs */
    public array $tags;
    /** @var array<int> Array of category IDs */
    public array $categorie;
    public ?string $percorso_allegato;

    public function __construct(
        string $idutente,
        int $idcorso,
        string $titolo,
        string $descrizione,
        array $tags = [],
        array $categorie = [],
        ?string $percorso_allegato = null,
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

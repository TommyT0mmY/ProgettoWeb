<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CreateUserPostDTO {
    public string $idutente;
    public int $idcorso;
    public string $titolo;
    public string $descrizione;
    /** @var array Array of tag arrays with 'idtag' and 'idcorso' keys */
    public array $tags;
    /** @var int Array of category IDs */
    public ?int $category;
    public ?string $percorso_allegato;

    public function __construct(
        string $idutente,
        int $idcorso,
        string $titolo,
        string $descrizione,
        array $tags = [],
        ?int $category = null,
        ?string $percorso_allegato = null,
    ) {
        $this->idutente = $idutente;
        $this->idcorso = $idcorso;
        $this->titolo = $titolo;
        $this->descrizione = $descrizione;
        $this->percorso_allegato = $percorso_allegato;
        $this->tags = $tags;
        $this->category = $category;
    }
}

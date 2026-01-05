<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PostDTO {
    public UserDTO $author;
    public int $idpost;
    public string $titolo;
    public string $descrizione;
    public ?string $percorso_allegato;
    public string $data_creazione;
    public string $idutente;
    public int $idcorso;
    /** @var array Array of tag arrays with 'tipo' keys */
    public array $tags;
    /** @var ?int  category id */
    public ?int $category;
    public int $likes;
    public int $dislikes;
    /** @var bool value is 0 if disliked, 1 if liked, null if no action taken */ 
    public ?bool $likedByUser;

    public function __construct(
        int $idpost,
        string $titolo,
        string $descrizione,
        string $data_creazione,
        string $idutente,
        int $idcorso,
        ?int $category,
        array $tags = [],
        int $likes = 0,
        int $dislikes = 0,
        ?bool $likedByUser = null,
        ?string $percorso_allegato = null
    ) {
        $this->idpost = $idpost;
        $this->titolo = $titolo;
        $this->descrizione = $descrizione;
        $this->percorso_allegato = $percorso_allegato;
        $this->data_creazione = $data_creazione;
        $this->idutente = $idutente;
        $this->idcorso = $idcorso;
        $this->tags = $tags;
        $this->category = $category;
        $this->likes = $likes;
        $this->dislikes = $dislikes;
        $this->likedByUser = $likedByUser;
    }
}

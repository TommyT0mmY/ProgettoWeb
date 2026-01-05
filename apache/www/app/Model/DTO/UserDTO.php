<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class UserDTO {
    public string $idutente;
    public bool $utente_sospeso;
    public ?string $nome;
    public ?string $cognome;
    public ?int $idfacolta;
    public ?string $password;

    public function __construct(
        string $idutente,
        ?string $nome,
        ?string $cognome,
        ?int $idfacolta,
        ?string $password,
        bool $utente_sospeso = false,
    ) {
        $this->idutente = $idutente;
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->idfacolta = $idfacolta;
        $this->utente_sospeso = $utente_sospeso;
        $this->password = $password;
    }
}

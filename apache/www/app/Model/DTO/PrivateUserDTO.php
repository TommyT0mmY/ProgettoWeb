<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PrivateUserDTO {
    public string $idutente;
    public string $nome;
    public string $cognome;
    public int $idfacolta;
    public bool $utente_sospeso;
    public string $password;

    public function __construct(
        string $idutente,
        string $nome,
        string $cognome,
        int $idfacolta,
        bool $utente_sospeso,
        string $password
    ) {
        $this->idutente = $idutente;
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->idfacolta = $idfacolta;
        $this->utente_sospeso = $utente_sospeso;
        $this->password = $password;
    }
}

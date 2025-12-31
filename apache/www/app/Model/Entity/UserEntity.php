<?php
declare(strict_types=1);

namespace Unibostu\Model\Entity;

class UserEntity {
    public string $idutente;
    public int $identita;
    public string $password;
    public string $nome;
    public string $cognome;
    public int $idfacolta;
    public bool $utente_sospeso;

    public function __construct(
        string $idutente,
        int $identita,
        string $password,
        string $nome,
        string $cognome,
        int $idfacolta,
        bool $utente_sospeso
    ) {
        $this->idutente = $idutente;
        $this->identita = $identita;
        $this->password = $password;
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->idfacolta = $idfacolta;
        $this->utente_sospeso = $utente_sospeso;
    }
}
<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class CreateUserDTO {
    public string $idutente;
    public string $password;
    public string $nome;
    public string $cognome;
    public int $idfacolta;

    public function __construct(
        string $idutente,
        string $password,
        string $nome,
        string $cognome,
        int $idfacolta
    ) {
        $this->idutente = $idutente;
        $this->password = $password;
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->idfacolta = $idfacolta;
    }
}

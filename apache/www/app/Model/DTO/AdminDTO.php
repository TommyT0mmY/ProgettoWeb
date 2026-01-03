<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class AdminDTO {
    public string $idamministratore;
    public string $password;

    public function __construct(
        string $idamministratore,
        string $password
    ) {
        $this->idamministratore = $idamministratore;
        $this->password = $password;
    }
}


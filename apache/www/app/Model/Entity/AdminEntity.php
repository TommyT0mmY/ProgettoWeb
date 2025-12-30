<?php
declare(strict_types=1);

namespace Unibostu\Model\Entity;

class AdminEntity {
    public string $idamministratore;
    public int $identita;
    public string $password;

    public function __construct(
        string $idamministratore,
        int $identita,
        string $password
    ) {
        $this->idamministratore = $idamministratore;
        $this->identita = $identita;
        $this->password = $password;
    }
}

?>

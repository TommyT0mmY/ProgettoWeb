<?php
declare(strict_types=1);

namespace Unibostu\Model\Entity;

class FacultyEntity {
    public int $idfacolta;
    public string $nome_facolta;

    public function __construct(
        int $idfacolta,
        string $nome_facolta
    ) {
        $this->idfacolta = $idfacolta;
        $this->nome_facolta = $nome_facolta;
    }
}


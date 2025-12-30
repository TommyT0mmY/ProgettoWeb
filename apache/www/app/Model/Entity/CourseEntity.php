<?php
declare(strict_types=1);

namespace Unibostu\Model\Entity;

class CourseEntity {
    public int $idcorso;
    public string $nome_corso;
    public int $idfacolta;

    public function __construct(
        int $idcorso,
        string $nome_corso,
        int $idfacolta
    ) {
        $this->idcorso = $idcorso;
        $this->nome_corso = $nome_corso;
        $this->idfacolta = $idfacolta;
    }
}

?>

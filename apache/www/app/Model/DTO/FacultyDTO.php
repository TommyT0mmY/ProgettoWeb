<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\FacultyEntity;

class FacultyDTO {
    public FacultyEntity $faculty;

    public function __construct(FacultyEntity $faculty) {
        $this->faculty = $faculty;
    }
}


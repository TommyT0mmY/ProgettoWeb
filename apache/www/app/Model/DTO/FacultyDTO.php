<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class FacultyDTO {
    public int $facultyId;
    public string $facultyName;

    public function __construct(
        int $facultyId,
        string $facultyName
    ) {
        $this->facultyId = $facultyId;
        $this->facultyName = $facultyName;
    }
}


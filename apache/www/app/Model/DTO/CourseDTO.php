<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CourseDTO {
    public int $courseId;
    public string $courseName;
    public int $facultyId;
    public ?string $facultyName;

    public function __construct(
        int $courseId,
        string $courseName,
        int $facultyId,
        ?string $facultyName = null
    ) {
        $this->courseId = $courseId;
        $this->courseName = $courseName;
        $this->facultyId = $facultyId;
        $this->facultyName = $facultyName;
    }
}


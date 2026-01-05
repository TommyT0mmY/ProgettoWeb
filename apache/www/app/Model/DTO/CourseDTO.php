<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CourseDTO {
    public int $courseId;
    public string $courseName;
    public int $facultyId;

    public function __construct(
        int $courseId,
        string $courseName,
        int $facultyId
    ) {
        $this->courseId = $courseId;
        $this->courseName = $courseName;
        $this->facultyId = $facultyId;
    }
}


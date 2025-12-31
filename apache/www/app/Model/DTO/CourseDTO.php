<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\CourseEntity;

class CourseDTO {
    public CourseEntity $course;

    public function __construct(CourseEntity $course) {
        $this->course = $course;
    }
}


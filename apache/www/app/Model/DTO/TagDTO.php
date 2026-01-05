<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class TagDTO {
    public int $tagId;
    public string $type;
    public int $courseId;

    public function __construct(
        int $tagId,
        string $type,
        int $courseId
    ) {
        $this->tagId = $tagId;
        $this->type = $type;
        $this->courseId = $courseId;
    }
}

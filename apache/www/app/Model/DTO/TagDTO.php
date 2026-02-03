<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class TagDTO {
    public int $tagId;
    public string $tagName;
    public int $courseId;

    public function __construct(
        int $tagId,
        string $tagName,
        int $courseId
    ) {
        $this->tagId = $tagId;
        $this->tagName = $tagName;
        $this->courseId = $courseId;
    }
}

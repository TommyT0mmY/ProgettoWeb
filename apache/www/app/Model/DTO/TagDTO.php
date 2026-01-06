<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class TagDTO {
    public int $tagId;
    public string $tag_name;
    public int $courseId;

    public function __construct(
        int $tagId,
        string $tag_name,
        int $courseId
    ) {
        $this->tagId = $tagId;
        $this->tag_name = $tag_name;
        $this->courseId = $courseId;
    }
}

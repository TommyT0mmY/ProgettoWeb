<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CreatePostDTO {
    public string $userId;
    public int $courseId;
    public string $title;
    public string $description;
    /** @var array Array of tag arrays with 'tagId' key */
    public array $tags;
    /** @var int Array of category IDs */
    public ?int $category;

    public function __construct(
        string $userId,
        int $courseId,
        string $title,
        string $description,
        array $tags = [],
        ?int $category = null
    ) {
        $this->userId = $userId;
        $this->courseId = $courseId;
        $this->title = $title;
        $this->description = $description;
        $this->tags = $tags;
        $this->category = $category;
    }
}

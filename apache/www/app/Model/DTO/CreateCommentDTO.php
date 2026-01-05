<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CreateCommentDTO {
    public int $postId;
    public string $text;
    public string $userId;
    public ?int $parentCommentId;

    public function __construct(
        int $postId,
        string $text,
        string $userId,
        ?int $parentCommentId = null
    ) {
        $this->postId = $postId;
        $this->text = $text;
        $this->userId = $userId;
        $this->parentCommentId = $parentCommentId;
    }
}

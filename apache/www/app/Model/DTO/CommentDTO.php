<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CommentDTO {
    public int $commentId;
    public int $postId;
    public string $text;
    public string $createdAt;
    public bool $deleted;
    public string $userId;
    public ?int $parentCommentId;

    public function __construct(
        int $commentId,
        int $postId,
        string $text,
        string $createdAt,
        string $userId,
        bool $deleted = false,
        ?int $parentCommentId = null
    ) {
        $this->commentId = $commentId;
        $this->postId = $postId;
        $this->text = $text;
        $this->createdAt = $createdAt;
        $this->deleted = $deleted;
        $this->userId = $userId;
        $this->parentCommentId = $parentCommentId;
    }
}

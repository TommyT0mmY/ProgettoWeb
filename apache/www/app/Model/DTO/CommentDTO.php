<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CommentDTO {
    public UserDTO $author;
    public int $commentId;
    public int $postId;
    public string $text;
    public string $createdAt;
    public bool $deleted;
    public ?int $parentCommentId;

    public function __construct(
        UserDTO $author,
        int $commentId,
        int $postId,
        string $text,
        string $createdAt,
        bool $deleted = false,
        ?int $parentCommentId = null
    ) {
        $this->author = $author;
        $this->commentId = $commentId;
        $this->postId = $postId;
        $this->text = $text;
        $this->createdAt = $createdAt;
        $this->deleted = $deleted;
        $this->parentCommentId = $parentCommentId;
    }
}

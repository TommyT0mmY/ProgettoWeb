<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CommentWithAuthorDTO {
    public CommentDTO $comment;
    public UserDTO $author;

    public function __construct(CommentDTO $comment, UserDTO $author) {
        $this->comment = $comment;
        $this->author = $author;
    }
}

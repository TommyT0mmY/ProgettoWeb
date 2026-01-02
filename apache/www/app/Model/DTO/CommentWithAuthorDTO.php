<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class CommentWithAuthorDTO {
    public CommentDTO $comment;
    public UserProfileDTO $author;

    public function __construct(CommentDTO $comment, UserProfileDTO $author) {
        $this->comment = $comment;
        $this->author = $author;
    }
}

<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CommentWithAuthorDTO {
    public CommentDTO $comment;
    public PublicUserDTO $author;

    public function __construct(CommentDTO $comment, PublicUserDTO $author) {
        $this->comment = $comment;
        $this->author = $author;
    }
}

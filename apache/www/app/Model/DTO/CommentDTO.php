<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\CommentEntity;
use Unibostu\Model\Entity\UserEntity;

class CommentWithAuthorDTO {
    public CommentEntity $comment;
    public UserEntity $author;

    public function __construct(CommentEntity $comment, UserEntity $author) {
        $this->comment = $comment;
        $this->author = $author;
    }
}

class CommentsListDTO {
    /** @var CommentWithAuthorDTO[] */
    public array $comments;

    public function __construct(array $comments = []) {
        $this->comments = $comments;
    }

    public function addComment(CommentWithAuthorDTO $comment): void {
        $this->comments[] = $comment;
    }
}
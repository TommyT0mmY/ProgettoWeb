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

<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CommentsListDTO {
    /** @var CommentWithAuthorDTO[] */
    public array $comments;

    public function __construct(array $comments = []) {
        $this->comments = $comments;
    }
}

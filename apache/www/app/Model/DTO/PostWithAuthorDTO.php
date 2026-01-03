<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class PostWithAuthorDTO {
    public PostDTO $post;
    public PublicUserDTO $author;

    public function __construct(PostDTO $post, PublicUserDTO $author) {
        $this->post = $post;
        $this->author = $author;
    }
}

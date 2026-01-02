<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class PostWithAuthorDTO {
    public PostDTO $post;
    public UserProfileDTO $author;

    public function __construct(PostDTO $post, UserProfileDTO $author) {
        $this->post = $post;
        $this->author = $author;
    }
}

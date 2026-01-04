<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class PostWithAuthorDTO {
    public PostDTO $post;
    public UserDTO $author;

    public function __construct(PostDTO $post, UserDTO $author) {
        $this->post = $post;
        $this->author = $author;
    }
}

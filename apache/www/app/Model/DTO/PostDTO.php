<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\PostEntity;
use Unibostu\Model\Entity\UserEntity;

class PostWithAuthorDTO {
    public PostEntity $post;
    public UserEntity $author;

    public function __construct(PostEntity $post, UserEntity $author) {
        $this->post = $post;
        $this->author = $author;
    }
}

class PostListDTO {
    /** @var PostWithAuthorDTO[] */
    public array $posts;

    public function __construct(array $posts = []) {
        $this->posts = $posts;
    }

    public function addPost(PostWithAuthorDTO $post): void {
        $this->posts[] = $post;
    }
}

?>
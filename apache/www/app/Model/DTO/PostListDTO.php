<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

class PostListDTO {
    /** @var PostWithAuthorDTO[] */
    public array $posts;

    public function __construct(array $posts = []) {
        $this->posts = $posts;
    }
}

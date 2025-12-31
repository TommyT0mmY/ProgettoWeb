<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\PostEntity;
use Unibostu\Model\Entity\UserEntity;

class PostWithAuthorDTO {
    public PostEntity $post;
    public UserEntity $author;
    /** @var array Array of tag arrays with 'tipo' and 'idcorso' keys */
    public array $tags;
    /** @var array Array of category IDs */
    public array $categorie;
    /** @var array Array of faculty IDs for visibility */
    public array $facolta;

    public function __construct(PostEntity $post, UserEntity $author) {
        $this->post = $post;
        $this->author = $author;
        $this->tags = $post->tags;
        $this->categorie = $post->categorie;
        $this->facolta = $post->facolta;
    }
}

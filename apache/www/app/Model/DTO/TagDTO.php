<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\TagEntity;

class TagDTO {
    public TagEntity $tag;

    public function __construct(TagEntity $tag) {
        $this->tag = $tag;
    }
}

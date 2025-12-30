<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\UserEntity;

class UserProfileDTO {
    public UserEntity $user;

    public function __construct(UserEntity $user) {
        $this->user = $user;
    }
}

?>
<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

use Unibostu\Model\Entity\AdminEntity;

class AdminDTO {
    public AdminEntity $admin;

    public function __construct(AdminEntity $admin) {
        $this->admin = $admin;
    }
}

?>

<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class AdminDTO {
    public string $adminId;
    public string $password;

    public function __construct(
        string $adminId,
        string $password
    ) {
        $this->adminId = $adminId;
        $this->password = $password;
    }
}


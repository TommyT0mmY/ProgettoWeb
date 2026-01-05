<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class UserDTO {
    public string $userId;
    public bool $suspended;
    public ?string $firstName;
    public ?string $lastName;
    public ?int $facultyId;
    public ?string $password;

    public function __construct(
        string $userId,
        ?string $firstName,
        ?string $lastName,
        ?int $facultyId,
        ?string $password,
        bool $suspended = false,
    ) {
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->facultyId = $facultyId;
        $this->suspended = $suspended;
        $this->password = $password;
    }
}

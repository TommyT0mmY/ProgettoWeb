<?php
declare(strict_types=1);

namespace Unibostu\Model\DTO;

readonly class CreateUserDTO {
    public string $userId;
    public string $password;
    public string $firstName;
    public string $lastName;
    public int $facultyId;

    public function __construct(
        string $userId,
        string $password,
        string $firstName,
        string $lastName,
        int $facultyId
    ) {
        $this->userId = $userId;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->facultyId = $facultyId;
    }
}

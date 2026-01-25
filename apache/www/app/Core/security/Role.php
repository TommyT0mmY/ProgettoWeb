<?php
declare(strict_types=1);

namespace Unibostu\Core\security;

enum Role {
    case ADMIN;
    case USER;
    case GUEST;
}

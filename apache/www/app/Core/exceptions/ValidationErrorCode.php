<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

enum ValidationErrorCode {
    case USERNAME_ALREADY_EXISTS;
    case USERNAME_REQUIRED;
    case FACULTY_INVALID;
    case FACULTY_REQUIRED;
    case FIRSTNAME_REQUIRED;
    case LASTNAME_REQUIRED;
    case PASSWORD_REQUIRED;
}

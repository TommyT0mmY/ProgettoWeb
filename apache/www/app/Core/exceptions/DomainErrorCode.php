<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

enum DomainErrorCode {
    case GENERIC_ERROR;
    case USER_NOT_FOUND;
    case INVALID_CREDENTIALS;
    case ACCESS_DENIED;
    case USER_ALREADY_EXISTS;
    case INVALID_DATA;
}

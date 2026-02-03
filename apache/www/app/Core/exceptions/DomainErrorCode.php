<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

enum DomainErrorCode {
    case GENERIC_ERROR;
    case INVALID_CREDENTIALS;
    case ACCESS_DENIED;
    case USER_ALREADY_EXISTS;
    case DATABASE_ERROR;
    case NOT_COMMENT_OWNER;
    case NOT_POST_OWNER;
}

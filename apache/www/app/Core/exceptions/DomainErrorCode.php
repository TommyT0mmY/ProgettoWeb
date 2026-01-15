<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

enum DomainErrorCode: string {
    case GENERIC_ERROR = "An error occurred.";
    case USER_NOT_FOUND = "User not found.";
    case INVALID_CREDENTIALS = "Invalid credentials provided.";
    case ACCESS_DENIED = "Access denied.";
    case USER_ALREADY_EXISTS = "User already exists.";
    case INVALID_DATA = "Invalid data provided.";
}

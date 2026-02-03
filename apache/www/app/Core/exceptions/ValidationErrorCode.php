<?php
declare(strict_types=1);

namespace Unibostu\Core\exceptions;

enum ValidationErrorCode {
    case USERNAME_ALREADY_EXISTS;
    case USERNAME_REQUIRED;
    case FIRSTNAME_REQUIRED;
    case LASTNAME_REQUIRED;
    case FACULTY_INVALID;
    case FACULTY_REQUIRED;
    case CATEGORY_REQUIRED;
    case PASSWORD_REQUIRED;
    case PASSWORD_INVALID;
    case POST_ID_REQUIRED;
    case COMMENT_TEXT_REQUIRED;
    case COMMENT_ID_REQUIRED;
    case TITLE_REQUIRED;
    case DESCRIPTION_REQUIRED;
    case COURSE_INVALID;
    case COURSE_REQUIRED;
    case TAG_REQUIRED;
    case USER_NOT_FOUND;
    case USER_SUSPENDED;
    case INVALID_REQUEST;
    // File upload errors
    case FILE_TOO_LARGE;
    case FILE_TYPE_NOT_ALLOWED;
    case FILE_UPLOAD_ERROR;
    case FILE_MAX_COUNT_EXCEEDED;
    case FILE_NAME_TOO_LONG;
    // Attachment errors
    case ATTACHMENT_FILENAME_INVALID;
    case ATTACHMENT_NOT_FOUND;
    // Category errors
    case CATEGORY_NAME_REQUIRED;
    case CATEGORY_ALREADY_EXISTS;
    case CATEGORY_NOT_FOUND;
    // Course errors
    case COURSE_NAME_REQUIRED;
    case COURSE_NOT_FOUND;
    case TAG_NAME_REQUIRED;
    case TAG_NOT_FOUND;
    case TAG_ALREADY_EXISTS;
    case ENROLLMENT_NOT_FOUND;
    case USER_NOT_ENROLLED;
    case POST_NOT_FOUND;
    case COMMENT_NOT_FOUND;
    case INVALID_REACTION;
    case TAG_MISMATCH;
}

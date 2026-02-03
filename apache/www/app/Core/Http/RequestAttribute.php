<?php
declare(strict_types=1);

namespace Unibostu\Core\Http;

/**
 * Request attributes injected by middleware.
 *
 * Request attributes store additional information in the request object.
 * This enum defines the possible attributes that can be stored.
 */
enum RequestAttribute: string {
    /** URL path variables from route matching. */
    case PATH_VARIABLES = "path_variables";
    /** Authenticated role (USER, ADMIN, GUEST). */
    case ROLE = "role";
    /** Authenticated entity ID. */
    case ROLE_ID = "role_id";
    /** Validated request body fields. */
    case FIELDS = "fields";
}

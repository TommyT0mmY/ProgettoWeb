<?php
declare(strict_types=1);

namespace Unibostu\Core\Http;

/**
 * Request Attributes are used to store additional information in the request object.
 * This enum defines the possible attributes that can be stored in the request.
 */
enum RequestAttribute: string {
    /**
     * The URL path variables.
     */
    case PATH_VARIABLES = "path_variables";
    /**
     * The role of the client making the request.
     */
    case ROLE = "role";
    /**
     * The ID of the role of the client making the request.
     */
    case ROLE_ID = "role_id";
}

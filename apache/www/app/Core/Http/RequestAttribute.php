<?php
declare(strict_types=1);

namespace Unibostu\Core\Http;

enum RequestAttribute: string {
    case PARAMETERS = "parameters";
    case ROLE = "role";
    case ROLE_ID = "role_id";
}

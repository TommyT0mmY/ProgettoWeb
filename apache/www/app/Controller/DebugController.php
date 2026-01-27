<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\security\Role;

class DebugController extends BaseController {

    #[Get("/debug")]
    #[AuthMiddleware(Role::GUEST, Role::USER, Role::ADMIN)]
    public function debug(Request $request): Response {
        ob_start();
        echo "<h1>Debug Information</h1>";
        echo "<h2>Request Data</h2>";
        echo "<pre>" . htmlspecialchars(print_r($request, true)) . "</pre
>";
        echo "<h2>Server Data</h2>";
        echo "<pre>" . htmlspecialchars(print_r($_SERVER, true)) . "</pre>";
        return new Response(ob_get_clean());
    }
}

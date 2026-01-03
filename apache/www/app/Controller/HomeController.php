<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;

class HomeController extends BaseController {
    public function index(): Response {
        return new Response("Welcome to the Home Page!");
    }

    public function userid(array $params, Request $request): Response {
        $userId = $params['id'] ?? 'unknown';
        return new Response("User ID: " . htmlspecialchars($userId) . "Request Method: " . htmlspecialchars($request->getMethod()));
    }
}


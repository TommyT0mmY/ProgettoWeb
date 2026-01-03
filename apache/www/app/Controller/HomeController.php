<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\CsrfProtection;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\Response;

class HomeController extends BaseController {
    public function index(): Response {
        $csrfman = $this->container->get(CsrfProtection::class);
        $csrftoken = $csrfman->generateToken('home_page_form');
        $validity = $csrfman->validateToken($csrftoken, 'home_page_form') ? 'valid' : 'invalid';
        return new Response("Welcome to the Home Page! $csrftoken | $validity");
    }

    public function userid(array $params, Request $request): Response {
        $userId = $params['id'] ?? 'unknown';
        return new Response("User ID: " . htmlspecialchars($userId) . "Request Method: " . htmlspecialchars($request->getMethod()));
    }
}


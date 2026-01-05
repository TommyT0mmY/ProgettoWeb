<?php
declare(strict_types=1);

namespace Unibostu\Controller;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;

class AuthController extends BaseController {
    #[Get("/login")]
    public function loginIndex(): Response {
        return $this->render("login", []);
    }

    #[Get("/register")]
    public function registerIndex(): Response {
        return $this->render("register", []);
    }

    #[Post("/api/auth/login")]
    public function login(array $params, Request $request): Response {
        return new Response("todo");
    } 

    #[Post("/api/auth/register")]
    public function register(array $params, Request $request): Response {
        return new Response("todo");
    }

    #[Post("/api/auth/logout")]
    public function logout(array $params, Request $request): Response {
        return new Response("todo");
    }
}

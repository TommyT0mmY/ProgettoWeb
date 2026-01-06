<?php
declare(strict_types=1);

namespace Unibostu\Controller;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\CsrfProtection;

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
        $username = $request->post("username");
        $password = $request->post("password");
        $csrfKey = $request->post("csrf-key");
        $csrfToken = $request->post("csrf-token");
        /** @var Auth $auth */
        $auth = $this->container->get(Auth::class);
        /** @var CsrfProtection $csrfProtection */
        $csrfProtection = $this->container->get(CsrfProtection::class);
        $success = true;
        if (!$csrfProtection->validateToken($csrfKey, $csrfToken)) {
            $success = false; 
        }
        if ($success && !$auth->loginAsUser($username, $password)) {
            $success = false;
        }
        $resultMessage = [];
        if ($success) {
            $csrfProtection->invalidateToken($csrfKey);
            $resultMessage = [
                "success" => true,
                "redirect" => "/",
            ];
        } else {
            $resultMessage = [
                "success" => false,
                "generalError" => "Invalid username or password.",
            ];
        }
        return Response::create()->json($resultMessage);
    } 

    #[Post("/api/auth/register")]
    public function register(array $params, Request $request): Response {
        $username = $request->post("username");
        $password = $request->post("password");
        $csrfKey = $request->post("csrf-key");
        $csrfToken = $request->post("csrf-token");
        /** @var Auth $auth */
        $auth = $this->container->get(Auth::class);
        /** @var CsrfProtection $csrfProtection */
        $csrfProtection = $this->container->get(CsrfProtection::class);
        $success = true;
        if (!$csrfProtection->validateToken($csrfKey, $csrfToken)) {
            $success = false; 
        }
        if ($success && !$auth->registerUser($username, $password)) {
            $success = false;
        }
        $resultMessage = [];
        if ($success) {
            $csrfProtection->invalidateToken($csrfKey);
            $resultMessage = [
                "success" => true,
                "redirect" => "/",
            ];
        } else {
            $resultMessage = [
                "success" => false,
                "generalError" => "Registration failed. Username may already be taken.",
            ];
        }
        return Response::create()->json($resultMessage);
    }

    #[Post("/api/auth/logout")]
    public function logout(array $params, Request $request): Response {
        /** @var Auth $auth */
        $auth = $this->container->get(Auth::class);
        $auth->logout();
        return Response::create()->json([
            "success" => true,
            "redirect" => "/login",
        ]);
    }
}

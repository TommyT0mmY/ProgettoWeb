<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\exceptions\DomainErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\Auth;
use Unibostu\Core\security\CsrfProtection;
use Unibostu\Core\security\Role;
use Unibostu\Model\DTO\UserDTO;
use Unibostu\Model\Service\FacultyService;
use Unibostu\Model\Service\UserService;

class AuthController extends BaseController {
    private CsrfProtection $csrfProtection;
    private Auth $auth;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->csrfProtection = $container->get(CsrfProtection::class);
        $this->auth = $container->get(Auth::class);
    }

    #[Get("/login")]
    public function loginIndex(): Response {
        return $this->render("login", []);
    }

    #[Get("/adminlogin")]
    public function adminloginIndex(): Response {
        return $this->render("adminlogin", []);
    }

    #[Get("/register")]
    public function registerIndex(): Response {
        $facultyService = new FacultyService();
        $faculties = $facultyService->getAllFaculties();
        return $this->render("register", ["faculties" => $faculties]);
    }

    #[Post("/api/auth/login")]
    public function login(Request $request): Response {
        $username = $request->post("username");
        $password = $request->post("password");

        if (!$this->csrfProtection->validateRequest($request)) {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::GENERIC_ERROR->name]
            ]);
        }
        $success = $this->auth->login(Role::USER, $username, $password);
        if ($success) {
            return Response::create()->json([
                "success" => true,
                "redirect" => "/",
            ]);
        } else {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::INVALID_CREDENTIALS->name]
            ]);
        }
    } 

    #[Post("/api/auth/adminlogin")]
    public function adminlogin(Request $request): Response {
        $username = $request->post("username");
        $password = $request->post("password");

        if (!$this->csrfProtection->validateRequest($request)) {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::GENERIC_ERROR->name]
            ]);
        }
        $success = $this->auth->login(Role::ADMIN, $username, $password);
        if ($success) {
            return Response::create()->json([
                "success" => true,
                "redirect" => "/",
            ]);
        } else {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::INVALID_CREDENTIALS->name],
            ]);
        }
    } 

    #[Post("/api/auth/register")]
    public function register(Request $request): Response {
        $userService = new UserService();
        $username = $request->post("username");
        $firstname = $request->post("firstname");
        $lastname = $request->post("lastname");
        $facultyid = $request->post("facultyid");
        $password = $request->post("password");

        if (!$this->csrfProtection->validateRequest($request)) {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::GENERIC_ERROR->name]
            ]);
        }
        try {
            $userService->registerUser(new UserDTO(
                userId: $username,
                firstName: $firstname, 
                lastName: $lastname,
                facultyId: (int)$facultyid,
                password: $password
            ));
        } catch (ValidationException $e) {
            return Response::create()->json([
                "success" => false,
                "errors" => $e->getErrorCodes()
            ]);
        }
        return Response::create()->json([
            "success" => true,
            "redirect" => "/login",
        ]);
    }

    #[Post("/api/auth/logout")]
    public function logout(): Response {
        $this->auth->logout();
        return Response::create()->json([
            "success" => true,
            "redirect" => "/login",
        ]);
    }
}

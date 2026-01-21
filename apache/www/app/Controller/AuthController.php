<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\exceptions\DomainErrorCode;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\CsrfProtection;
use Unibostu\Model\DTO\UserDTO;
use Unibostu\Model\Service\FacultyService;
use Unibostu\Model\Service\UserService;

class AuthController extends BaseController {
    private CsrfProtection $csrfProtection;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->csrfProtection = $container->get(CsrfProtection::class);
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
    public function login(array $params, Request $request): Response {
        $username = $request->post("username");
        $password = $request->post("password");

        if (!$this->csrfProtection->validateRequest($request)) {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::GENERIC_ERROR->name]
            ]);
        }
        $success = $this->getAuth()->loginAsUser($username, $password);
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
    public function adminlogin(array $params, Request $request): Response {
        $username = $request->post("username");
        $password = $request->post("password");

        if (!$this->csrfProtection->validateRequest($request)) {
            return Response::create()->json([
                "success" => false,
                "errors" => [DomainErrorCode::GENERIC_ERROR->name]
            ]);
        }
        $success = $this->getAuth()->loginAsAdmin($username, $password);
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
    public function register(array $params, Request $request): Response {
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
    public function logout(array $params, Request $request): Response {
        $this->getAuth()->logout();
        return Response::create()->json([
            "success" => true,
            "redirect" => "/login",
        ]);
    }
}

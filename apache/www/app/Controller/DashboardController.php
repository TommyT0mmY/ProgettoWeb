<?php
declare(strict_types=1);

namespace Unibostu\Controller;

use Unibostu\Core\Container;
use Unibostu\Core\exceptions\ValidationErrorCode;
use Unibostu\Core\exceptions\ValidationException;
use Unibostu\Core\Http\Response;
use Unibostu\Core\Http\Request;
use Unibostu\Core\Http\RequestAttribute;
use Unibostu\Core\router\middleware\AuthMiddleware;
use Unibostu\Core\router\middleware\ValidationMiddleware;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\FacultyService;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\TagService;

class DashboardController extends BaseController {   
    private UserService $userService;
    private CategoryService $categoryService;
    private FacultyService $facultyService;

    public function __construct(Container $container) {
        parent::__construct($container);       
        $this->userService = new UserService();
        $this->categoryService = new CategoryService();
        $this->facultyService = new FacultyService();
    }

    #[Get('/dashboard')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getDashboard(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);
        
        return $this->render("admin/dashboard", [
        'users' => $this->userService->getAllUsers(),        
        'categories' => $this->categoryService->getAllCategories(),
        'faculties' => $this->facultyService->getAllFaculties(),
        'adminId' => $adminId
        ]);
    }

    #[Get('/users')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getUsers(Request $request): Response {
        $adminId = $request->getAttribute(RequestAttribute::ROLE_ID);

        $users = $this->userService->getAllUsers();
        $faculties = [];
        foreach ($users as $user) {
            $faculties[$user->userId] = $this->facultyService->getFacultyDetails($user->facultyId);
        }
        
        return $this->render("admin/users", [
            'users' => $users,
            'adminId' => $adminId,
            'faculties' => $faculties
        ]);
    }    
}

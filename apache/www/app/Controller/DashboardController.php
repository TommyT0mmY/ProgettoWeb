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
use Unibostu\Model\DTO\PostQuery;
use Unibostu\Core\router\routes\Get;
use Unibostu\Core\router\routes\Post;
use Unibostu\Model\Service\PostService;
use Unibostu\Core\security\Role;
use Unibostu\Model\Service\CourseService;
use Unibostu\Model\Service\CategoryService;
use Unibostu\Model\Service\UserService;
use Unibostu\Model\Service\FacultyService;

class DashboardController extends BaseController {
    private $postService;
    private $courseService;
    private $userService;
    private $facultyService;
    private $categoryService;

    public function __construct(Container $container) {
        parent::__construct($container);
        $this->postService = new PostService();
        $this->courseService = new CourseService();
        $this->userService = new UserService();
        $this->facultyService = new FacultyService();
        $this->categoryService = new CategoryService();
    }

    /** get faculties */
    #[Get('/faculties')]
    #[AuthMiddleware(Role::ADMIN)]
    public function getFaculties(Request $request): Response {
        $userId = $request->getAttribute(RequestAttribute::ROLE_ID);
        $user = $this->userService->getUserProfile($userId);

        return $this->render("admin-faculties", [
            'user' => $user,
            'faculties' => $this->facultyService->getAllFaculties(),
            'courses' => $this->courseService->getAllCourses(),
            'userId' => $userId
        ]);
    }

    
}

